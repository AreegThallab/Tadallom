from fastapi import FastAPI
from pydantic import BaseModel
from typing import Optional, Literal
from datetime import datetime

# -------- Tools --------
def to_date(s: str):
    return datetime.strptime(s, "%Y-%m-%d").date()

CATEGORY_KEYWORDS = {
    "وظيفي": ["فصل", "إنهاء خدمة", "ترقية", "بدلات", "إيقاف", "خصم", "مباشرة", "نقل", "علاوة", "راتب"],
    "تعليمي": ["قبول", "منحة", "حرمان", "مكافأة", "فصل أكاديمي", "انسحاب", "اختبار", "نتيجة"],
    "خدمات/بلدية": ["رخصة", "إزالة", "مخالفة", "بلدية", "هدم", "إغلاق", "تصريح"],
    "استحقاقات/مالية": ["تعويض", "مستحقات", "صرف", "بدل", "علاوة", "مكافأة", "مطالبة مالية"],
    "أخرى": []
}

def infer_category(complaint_text: str):
    text = complaint_text.strip()
    scores = {}
    for cat, kws in CATEGORY_KEYWORDS.items():
        score = 0
        for kw in kws:
            if kw in text:
                score += 1
        scores[cat] = score

    best_cat = max(scores, key=scores.get)
    confidence = scores[best_cat]
    if confidence == 0:
        best_cat = "أخرى"
    return best_cat, confidence

def analyze_case(case):
    today = to_date(case["today"])
    grievance_submitted = case["grievance_submitted"]
    admin_response = case["admin_response"]
    response_type = case["response_type"]

    result = {"eligibility_status": "", "risk_level": "", "reasons": []}

    if not grievance_submitted:
        result["eligibility_status"] = "غير مؤهل شكليًا"
        result["risk_level"] = "عالي"
        result["reasons"].append("لم يتم تقديم التظلّم المسبق.")
        return result

    grievance_date = to_date(case["grievance_date"])
    days_passed = (today - grievance_date).days

    if admin_response and response_type == "موضوعي":
        result["eligibility_status"] = "اكتمل التظلّم (رد موضوعي) — مؤهل شكليًا"
        result["risk_level"] = "منخفض"
        result["reasons"].append("تم استلام رد إداري موضوعي.")
        return result

    if days_passed < 60:
        result["eligibility_status"] = "غير مكتمل بعد"
        result["risk_level"] = "متوسط"
        result["reasons"].append(f"لم تمضِ 60 يومًا (الماضي: {days_passed} يوم).")
        return result

    result["eligibility_status"] = "اكتمل التظلّم بانقضاء المدة — مؤهل شكليًا"
    result["risk_level"] = "منخفض"
    if not admin_response:
        result["reasons"].append("لم يرد الجهاز الإداري خلال 60 يومًا.")
    else:
        result["reasons"].append("الرد كان شكليًا وانقضت المدة.")
    return result

def generate_report(case, analysis):
    text = f"""تقرير التظلّم الإجرائي الذكي

تاريخ القرار الإداري: {case["decision_date"]}
تاريخ التظلّم: {case["grievance_date"]}
تاريخ التقييم: {case["today"]}

الحالة الإجرائية:
{analysis["eligibility_status"]}

مستوى الخطر:
{analysis["risk_level"]}

الأسباب:
"""
    for r in analysis["reasons"]:
        text += f"- {r}\n"

    if analysis["risk_level"] != "منخفض":
        text += "\n⚠️ تنبيه: يوجد خطر رفض شكلي في حال رفع الدعوى الآن."
    else:
        text += "\n✅ التوصية: الإجراءات مكتملة شكليًا ويمكن رفع الدعوى."
    return text

# -------- API --------
class CaseInputV2(BaseModel):
    complaint_text: str
    decision_date: str
    grievance_submitted: bool
    grievance_date: Optional[str] = None
    admin_response: bool
    response_type: Literal["موضوعي", "شكلي", "لا يوجد"]
    today: str

app = FastAPI(title="Tazallom AI")

@app.get("/")
def root():
    return {"ok": True}

@app.post("/analyze_v2")
def analyze_v2(payload: CaseInputV2):
    p = payload.model_dump()

    cat, conf = infer_category(p["complaint_text"])
    inferred = {"category": cat, "confidence": conf}

    if p["grievance_submitted"] and not p["grievance_date"]:
        return {"inferred": inferred, "error": "grievance_date مطلوب عند تقديم التظلّم"}

    analysis = analyze_case(p)
    report = generate_report(p, analysis)

    return {"inferred": inferred, "analysis": analysis, "report": report}
