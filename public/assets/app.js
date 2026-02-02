// Scroll reveal
const io = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if (e.isIntersecting) e.target.classList.add("in");
  });
}, { threshold: 0.12 });

document.querySelectorAll(".reveal").forEach(el => io.observe(el));

// Subtle parallax for elements
const parallaxEls = document.querySelectorAll("[data-parallax]");
window.addEventListener("mousemove", (e) => {
  const x = (e.clientX / window.innerWidth) - 0.5;
  const y = (e.clientY / window.innerHeight) - 0.5;

  parallaxEls.forEach(el => {
    const s = Number(el.getAttribute("data-parallax")) || 6;
    el.style.transform = `translate(${x * s}px, ${y * s}px)`;
  });
}, { passive: true });

/* ===== Old nav (menu) active link (if exists) ===== */
const oldMenuLinks = document.querySelectorAll(".menu a");
if (oldMenuLinks.length) {
  oldMenuLinks.forEach(a => {
    a.addEventListener("click", () => {
      document.querySelectorAll(".menu a").forEach(x => x.classList.remove("active"));
      a.classList.add("active");
    });
  });
}

/* ===== New Wateed-like landing nav pill animation ===== */
const landingNav = document.querySelector(".landing-links");
if (landingNav) {
  const links = Array.from(landingNav.querySelectorAll("a"));

  function setActive(link) {
    links.forEach(a => a.classList.remove("is-active"));
    link.classList.add("is-active");

    const r = link.getBoundingClientRect();
    const nr = landingNav.getBoundingClientRect();
    landingNav.style.setProperty("--pill-left", `${r.left - nr.left}px`);
    landingNav.style.setProperty("--pill-width", `${r.width}px`);
  }

  function pickByHash() {
    const h = window.location.hash || "";
    if (h) {
      const match = links.find(a => (a.getAttribute("href") || "").includes(h));
      if (match) return setActive(match);
    }
    // fallback: أول رابط أو اللي عليه is-active
    const preset = links.find(a => a.classList.contains("is-active"));
    setActive(preset || links[0]);
  }

  // click
  links.forEach(a => a.addEventListener("click", () => setActive(a)));

  // resize + hashchange
  window.addEventListener("resize", pickByHash);
  window.addEventListener("hashchange", pickByHash);

  // init after paint (عشان القياسات تكون صحيحة)
  requestAnimationFrame(pickByHash);
}
// Simple reveal for feature cards
const cards = document.querySelectorAll(".js-reveal");
const io2 = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if (e.isIntersecting) e.target.classList.add("in");
  });
}, { threshold: 0.15 });

cards.forEach(c => io2.observe(c));
// ===== Accordion =====
document.querySelectorAll("[data-acc]").forEach(btn => {
  btn.addEventListener("click", () => {
    const acc = btn.closest(".accordion");
    if (!acc) return;
    acc.classList.toggle("open");
  });
});

// ===== Tabs =====
document.querySelectorAll("[data-tabs]").forEach(tabs => {
  const tabBtns = tabs.querySelectorAll("[data-tab]");
  const panels = tabs.querySelectorAll("[data-panel]");

  tabBtns.forEach(btn => {
    btn.addEventListener("click", () => {
      const key = btn.getAttribute("data-tab");

      tabBtns.forEach(b => b.classList.remove("active"));
      btn.classList.add("active");

      panels.forEach(p => p.classList.toggle("show", p.getAttribute("data-panel") === key));
    });
  });
});

// ===== Copy Report =====
document.querySelectorAll("[data-copy]").forEach(btn => {
  btn.addEventListener("click", async () => {
    const sel = btn.getAttribute("data-copy");
    const el = document.querySelector(sel);
    if (!el) return;

    try{
      await navigator.clipboard.writeText(el.value || el.textContent || "");
      btn.textContent = "تم النسخ ✅";
      setTimeout(()=> btn.textContent="نسخ التقرير", 1200);
    }catch(e){
      alert("ما قدرنا ننسخ. جرّبي من المتصفح مباشرة.");
    }
  });
});
