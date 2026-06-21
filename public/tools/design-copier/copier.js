/* =============================================================================
   Design Copier — client-side design-token extraction engine.
   Paste any site's HTML source; we extract colours, fonts & feel, then theme
   the boiler template live. No network calls, no uploads — 100% in-browser.
   ============================================================================= */
(function () {
  'use strict';

  var $ = function (s) { return document.querySelector(s); };
  var srcEl = $('#src'), statusEl = $('#status'), scanEl = $('#scanlist');
  var swatchEl = $('#swatches'), tunerEl = $('#tuner'), cssOut = $('#cssOut');
  var preview = $('#preview'), stage = document.getElementById('hiddenStage');

  /* Default tokens (mirror the real template) ------------------------------- */
  var DEFAULTS = {
    brand: '#0b5cab', brandDark: '#073f76', brandLight: '#e8f1fb',
    accent: '#ff7a18', accentDark: '#e2620b',
    ink: '#0e1726', body: '#41506b', line: '#e3e8f0',
    bg: '#ffffff', bgAlt: '#f5f8fc',
    font: 'system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif',
    radius: '14px'
  };
  var tokens = Object.assign({}, DEFAULTS);

  /* ---- Colour helpers ----------------------------------------------------- */
  function clamp(n){ return Math.max(0, Math.min(255, n)); }
  function toHex(r,g,b){ return '#' + [r,g,b].map(function(x){ return clamp(Math.round(x)).toString(16).padStart(2,'0'); }).join(''); }

  function parseColor(str){
    if(!str) return null;
    str = str.trim().toLowerCase();
    var m;
    if((m = str.match(/^#([0-9a-f]{3})$/))){ var h=m[1]; return {r:parseInt(h[0]+h[0],16),g:parseInt(h[1]+h[1],16),b:parseInt(h[2]+h[2],16),a:1}; }
    if((m = str.match(/^#([0-9a-f]{6})([0-9a-f]{2})?$/))){ var x=m[1]; return {r:parseInt(x.slice(0,2),16),g:parseInt(x.slice(2,4),16),b:parseInt(x.slice(4,6),16),a:m[2]?parseInt(m[2],16)/255:1}; }
    if((m = str.match(/^rgba?\(([^)]+)\)$/))){ var p=m[1].split(/[ ,/]+/).filter(Boolean); return {r:+p[0],g:+p[1],b:+p[2],a:p[3]!==undefined?+p[3]:1}; }
    if((m = str.match(/^hsla?\(([^)]+)\)$/))){ var q=m[1].split(/[ ,/]+/).filter(Boolean); var c=hslToRgb(parseFloat(q[0]),parseFloat(q[1]),parseFloat(q[2])); c.a=q[3]!==undefined?+q[3]:1; return c; }
    return null;
  }
  function hslToRgb(h,s,l){ h=(h%360+360)%360; s/=100; l/=100; var c=(1-Math.abs(2*l-1))*s, x=c*(1-Math.abs((h/60)%2-1)), m=l-c/2, r=0,g=0,b=0;
    if(h<60){r=c;g=x;} else if(h<120){r=x;g=c;} else if(h<180){g=c;b=x;} else if(h<240){g=x;b=c;} else if(h<300){r=x;b=c;} else {r=c;b=x;}
    return {r:(r+m)*255,g:(g+m)*255,b:(b+m)*255}; }

  function luminance(c){ var a=[c.r,c.g,c.b].map(function(v){ v/=255; return v<=0.03928?v/12.92:Math.pow((v+0.055)/1.055,2.4); }); return 0.2126*a[0]+0.7152*a[1]+0.0722*a[2]; }
  function isGrey(c){ var mx=Math.max(c.r,c.g,c.b),mn=Math.min(c.r,c.g,c.b); return (mx-mn)<18; }
  function saturation(c){ var mx=Math.max(c.r,c.g,c.b)/255,mn=Math.min(c.r,c.g,c.b)/255; var l=(mx+mn)/2; if(mx===mn) return 0; return l>0.5?(mx-mn)/(2-mx-mn):(mx-mn)/(mx+mn); }
  function shade(c, amt){ /* amt -1..1 ; neg=darker */ return toHex(c.r+(amt<0?c.r*amt:(255-c.r)*amt), c.g+(amt<0?c.g*amt:(255-c.g)*amt), c.b+(amt<0?c.b*amt:(255-c.b)*amt)); }
  function tint(c, amt){ return toHex(c.r+(255-c.r)*amt, c.g+(255-c.g)*amt, c.b+(255-c.b)*amt); }
  function hex(c){ return toHex(c.r,c.g,c.b); }

  /* ---- Extraction --------------------------------------------------------- */
  function scanRawColors(text){
    var re = /#[0-9a-fA-F]{3,8}\b|rgba?\([^)]+\)|hsla?\([^)]+\)/g, m, counts = {};
    while((m = re.exec(text))){
      var c = parseColor(m[0]); if(!c || c.a < 0.5) continue;
      var key = hex(c); counts[key] = (counts[key]||0)+1;
    }
    return Object.keys(counts).map(function(k){ return {hex:k, n:counts[k], c:parseColor(k)}; })
                 .sort(function(a,b){ return b.n - a.n; });
  }

  function pickFromComputed(doc){
    var out = {};
    try{
      var body = doc.body; if(!body) return out;
      var bs = doc.defaultView.getComputedStyle(body);
      out.bg = parseColor(bs.backgroundColor);
      out.body = parseColor(bs.color);
      out.font = bs.fontFamily;
      var h = doc.querySelector('h1,h2,h3'); if(h) out.ink = parseColor(doc.defaultView.getComputedStyle(h).color);
      // brand: most saturated colour among buttons/links/headers backgrounds & text
      var cand = doc.querySelectorAll('a,button,.btn,header,nav,[class*=btn],[class*=cta],[class*=hero]');
      var best=null, bestAcc=null;
      Array.prototype.forEach.call(cand, function(el){
        var cs = doc.defaultView.getComputedStyle(el);
        [cs.backgroundColor, cs.color, cs.borderColor].forEach(function(v){
          var c = parseColor(v); if(!c||c.a<0.6||isGrey(c)) return;
          var sat = saturation(c), lum = luminance(c);
          if(lum>0.92||lum<0.03) return;
          if(!best || sat>saturation(best)) { if(best && saturation(best)>0.35){ if(!bestAcc||sat>saturation(bestAcc)) bestAcc=best; } best=c; }
          else if(sat>0.4 && (!bestAcc||sat>saturation(bestAcc))) bestAcc=c;
        });
      });
      if(best) out.brand = best;
      if(bestAcc && hex(bestAcc)!==hex(best||{})) out.accent = bestAcc;
    }catch(e){}
    return out;
  }

  function reconcile(raw, computed){
    var t = Object.assign({}, DEFAULTS);
    // colourful (non-grey, mid-luminance) raw colours, weighted by frequency
    var colourful = raw.filter(function(o){ return !isGrey(o.c) && o.c.a>=0.8 && luminance(o.c)<0.9 && luminance(o.c)>0.03 && saturation(o.c)>0.18; });

    var brand = (computed.brand && !isGrey(computed.brand)) ? computed.brand : (colourful[0] && colourful[0].c);
    if(brand){ t.brand=hex(brand); t.brandDark=shade(brand,-0.35); t.brandLight=tint(brand,0.88); }

    // accent = a different hue from brand, prefer warm/saturated
    var accent = computed.accent;
    if(!accent){
      for(var i=0;i<colourful.length;i++){
        var c=colourful[i].c; if(!brand || hueDist(c,brand)>40){ accent=c; break; }
      }
    }
    if(accent){ t.accent=hex(accent); t.accentDark=shade(accent,-0.3); }

    if(computed.ink && luminance(computed.ink)<0.4) t.ink=hex(computed.ink);
    if(computed.body) t.body=hex(computed.body);
    if(computed.bg && luminance(computed.bg)>0.85){ t.bg=hex(computed.bg); t.bgAlt=shade(computed.bg,-0.03); }
    if(computed.font && computed.font.length>2){ t.font=computed.font; }
    t.line = shade(parseColor(t.bgAlt)||{r:240,g:244,b:250}, -0.06);
    return t;
  }
  function hueOf(c){ var r=c.r/255,g=c.g/255,b=c.b/255,mx=Math.max(r,g,b),mn=Math.min(r,g,b),d=mx-mn,h=0; if(d===0)h=0; else if(mx===r)h=((g-b)/d)%6; else if(mx===g)h=(b-r)/d+2; else h=(r-g)/d+4; h*=60; return (h+360)%360; }
  function hueDist(a,b){ var d=Math.abs(hueOf(a)-hueOf(b)); return Math.min(d,360-d); }

  /* ---- Render preview / outputs ------------------------------------------ */
  function renderSwatches(){
    var defs = [
      ['brand','Primary brand'],['accent','Accent / CTA'],['ink','Headings'],
      ['body','Body text'],['bg','Background'],['bgAlt','Alt background'],['line','Borders']
    ];
    swatchEl.innerHTML = defs.map(function(d){
      var v = tokens[d[0]];
      return '<div class="sw"><div class="chip" style="background:'+v+'"></div>'+
             '<div class="meta"><b>'+d[1]+'</b><code data-copy="'+v+'">'+v+'</code></div></div>';
    }).join('');
    swatchEl.querySelectorAll('[data-copy]').forEach(function(el){
      el.addEventListener('click', function(){ navigator.clipboard && navigator.clipboard.writeText(el.dataset.copy); el.textContent='copied!'; setTimeout(function(){el.textContent=el.dataset.copy;},900); });
    });
  }

  function renderTuner(){
    var rows = [['brand','Brand'],['accent','Accent'],['ink','Headings'],['body','Body'],['bg','Background']];
    tunerEl.innerHTML = rows.map(function(r){
      return '<div class="tokrow"><label>'+r[1]+'</label><span><input type="color" data-tok="'+r[0]+'" value="'+tokens[r[0]]+'"><span class="val">'+tokens[r[0]]+'</span></span></div>';
    }).join('');
    tunerEl.querySelectorAll('input[type=color]').forEach(function(inp){
      inp.addEventListener('input', function(){
        var c = parseColor(inp.value); tokens[inp.dataset.tok]=inp.value;
        if(inp.dataset.tok==='brand'){ tokens.brandDark=shade(c,-0.35); tokens.brandLight=tint(c,0.88); }
        if(inp.dataset.tok==='accent'){ tokens.accentDark=shade(c,-0.3); }
        if(inp.dataset.tok==='bg'){ tokens.bgAlt=shade(c,-0.03); tokens.line=shade(c,-0.08); }
        inp.nextElementSibling.textContent=inp.value;
        apply();
      });
    });
  }

  function cssText(){
    return ':root{\n'+
      '  --color-brand: '+tokens.brand+';\n'+
      '  --color-brand-dark: '+tokens.brandDark+';\n'+
      '  --color-brand-light: '+tokens.brandLight+';\n'+
      '  --color-accent: '+tokens.accent+';\n'+
      '  --color-accent-dark: '+tokens.accentDark+';\n'+
      '  --color-ink: '+tokens.ink+';\n'+
      '  --color-body: '+tokens.body+';\n'+
      '  --color-line: '+tokens.line+';\n'+
      '  --color-bg: '+tokens.bg+';\n'+
      '  --color-bg-alt: '+tokens.bgAlt+';\n'+
      '  --font: '+tokens.font+';\n'+
      '}';
  }

  function previewDoc(){
    return '<!doctype html><html><head><meta charset="utf-8"><style>'+
      '*{box-sizing:border-box;margin:0}body{font-family:'+tokens.font+';color:'+tokens.body+';background:'+tokens.bg+'}'+
      '.h{background:linear-gradient(180deg,'+tokens.brandLight+',#fff);padding:22px 20px 26px;border-bottom:1px solid '+tokens.line+'}'+
      '.nav{display:flex;justify-content:space-between;align-items:center;font-weight:800;color:'+tokens.ink+'}'+
      '.nav b span{color:'+tokens.brand+'}'+
      '.pill{display:inline-block;font-size:11px;font-weight:700;color:'+tokens.accentDark+';background:#fff;border:1px solid '+tokens.line+';padding:4px 10px;border-radius:99px;margin-bottom:10px}'+
      'h1{color:'+tokens.ink+';font-size:26px;letter-spacing:-.02em;line-height:1.15;margin:6px 0 8px}'+
      'p.sub{color:'+tokens.body+';font-size:14px;max-width:34ch}'+
      '.btn{display:inline-block;font-weight:700;font-size:14px;padding:11px 18px;border-radius:99px;text-decoration:none;margin:14px 8px 0 0}'+
      '.btn.p{background:'+tokens.accent+';color:#fff}'+
      '.btn.g{border:2px solid '+tokens.line+';color:'+tokens.ink+'}'+
      '.sec{padding:22px 20px}.sec.alt{background:'+tokens.bgAlt+'}'+
      '.cards{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:12px}'+
      '.card{background:#fff;border:1px solid '+tokens.line+';border-radius:14px;padding:16px}'+
      '.ic{width:42px;height:42px;border-radius:10px;background:'+tokens.brandLight+';color:'+tokens.brand+';display:grid;place-items:center;margin-bottom:8px}'+
      '.card h3{color:'+tokens.ink+';font-size:15px;margin-bottom:4px}.card p{font-size:12.5px}'+
      '.price{border:2px solid '+tokens.accent+';border-radius:14px;padding:16px;text-align:center;max-width:260px}'+
      '.price .amt{font-size:30px;font-weight:800;color:'+tokens.ink+'}'+
      '.band{background:'+tokens.brandDark+';color:#fff;text-align:center;padding:24px 20px;border-radius:0}'+
      '.band h2{font-size:20px;margin-bottom:6px}.band p{color:#cfe0f3;font-size:13px}'+
      '</style></head><body>'+
      '<div class="h"><div class="nav"><b>BoilerCo<span>UK</span></b><span style="font-size:12px;color:'+tokens.brandDark+';font-weight:800">☎ 0800 000 0000</span></div>'+
        '<div style="margin-top:16px"><span class="pill">★ Rated 4.9/5 by homeowners</span>'+
        '<h1>A warm home by tomorrow, from a price you can trust</h1>'+
        '<p class="sub">Gas Safe engineers. Fixed-price quotes, next-day fitting & up to 12-year warranties.</p>'+
        '<a class="btn p" href="#">Get my fixed price →</a><a class="btn g" href="#">☎ Call us</a></div></div>'+
      '<div class="sec alt"><div class="cards">'+
        '<div class="card"><div class="ic">\u{1F525}</div><h3>New boilers</h3><p>A-rated, fitted next day with up to 12-yr warranty.</p></div>'+
        '<div class="card"><div class="ic">\u{1F6E0}</div><h3>Repairs</h3><p>Same-day callouts, fixed upfront pricing.</p></div>'+
      '</div></div>'+
      '<div class="sec" style="display:flex;justify-content:center"><div class="price"><div style="font-weight:800;color:'+tokens.ink+'">Most popular</div><div class="amt">£2,295</div><div style="font-size:12px;color:'+tokens.body+'">fitted • 0% finance</div><a class="btn p" style="display:block;margin:12px 0 0" href="#">Choose plan</a></div></div>'+
      '<div class="band"><h2>Don’t get left in the cold</h2><p>Get your no-obligation fixed quote in 60 seconds.</p><a class="btn p" style="margin-top:14px" href="#">Get my free quote →</a></div>'+
      '</body></html>';
  }

  function apply(){
    preview.srcdoc = previewDoc();
    cssOut.textContent = cssText();
    renderSwatches();
  }

  /* ---- Main extract flow -------------------------------------------------- */
  function extract(){
    var text = srcEl.value.trim();
    if(text.length < 20){ status('Paste a website’s source first.', 'warn'); return; }
    status('Analysing…');

    var raw = scanRawColors(text);
    // render in hidden stage to read computed styles (inline + <style> apply instantly)
    var safe = text.replace(/<script[\s\S]*?<\/script>/gi,'');           // strip scripts for safety
    try { stage.srcdoc = safe; } catch(e){ stage.removeAttribute('srcdoc'); }

    var done = function(){
      var computed = {};
      try { computed = pickFromComputed(stage.contentDocument || stage.contentWindow.document); } catch(e){}
      tokens = reconcile(raw, computed);
      renderSwatches(); renderTuner(); apply();
      showScan(raw);
      status('✓ Design extracted from '+raw.length+' colours — themed your template. Tweak below, then copy the CSS.', 'ok');
    };
    // give external <style>/inline a tick to compute; external CSS may load async
    stage.onload = function(){ setTimeout(done, 120); };
    setTimeout(done, 400); // fallback if onload doesn't fire (srcdoc)
  }

  function showScan(raw){
    if(!raw.length){ scanEl.hidden=true; return; }
    scanEl.hidden=false;
    scanEl.innerHTML = '<div style="color:#fff;font-weight:700;margin-bottom:.25rem">Top colours found</div>' +
      raw.slice(0,10).map(function(o){ return '<div><i style="background:'+o.hex+'"></i> '+o.hex+' <span style="color:#5d7296">×'+o.n+'</span></div>'; }).join('');
  }

  function status(msg, cls){ statusEl.textContent=msg; statusEl.className='hint'+(cls?' '+cls:''); }

  /* ---- Wire up ------------------------------------------------------------ */
  $('#extractBtn').addEventListener('click', extract);
  $('#resetBtn').addEventListener('click', function(){ tokens=Object.assign({},DEFAULTS); srcEl.value=''; renderTuner(); apply(); showScan([]); status('Reset to template defaults.'); });
  $('#sampleBtn').addEventListener('click', function(){
    srcEl.value = SAMPLE; extract();
  });
  $('#copyCss').addEventListener('click', function(){ navigator.clipboard && navigator.clipboard.writeText(cssText()); this.textContent='✓ Copied'; var b=this; setTimeout(function(){b.textContent='\u{1F4CB} Copy CSS';},1200); });
  $('#downloadCss').addEventListener('click', function(){
    var blob=new Blob([cssText()+'\n'],{type:'text/css'}); var a=document.createElement('a');
    a.href=URL.createObjectURL(blob); a.download='theme.css'; a.click(); URL.revokeObjectURL(a.href);
  });
  document.querySelectorAll('.device [data-w]').forEach(function(b){
    b.addEventListener('click', function(){ preview.style.maxWidth=b.dataset.w; });
  });

  /* A small built-in sample (a green/eco brand) to demo extraction */
  var SAMPLE = '<!doctype html><html><head><style>'+
    ':root{--g:#0f9d58;--g2:#0b7c45;--amber:#f4b400}body{font-family:Georgia,serif;color:#223;background:#fff}'+
    '.hero{background:#e7f7ee;padding:40px}h1{color:#103b29}'+
    '.btn{background:#f4b400;color:#fff;padding:12px 20px;border-radius:8px}'+
    'a{color:#0f9d58}.nav{background:#0b7c45;color:#fff}.cta{background:#0f9d58}'+
    '</style></head><body><div class="nav">Eco</div><div class="hero"><h1>Greener living</h1>'+
    '<a class="btn">Get started</a></div></body></html>';

  // init
  renderTuner(); apply(); status('Ready. Paste a site’s source and hit Extract — or click “Try a sample”.');
})();
