/* ══════════════════════════════════════════
   UTILITY
══════════════════════════════════════════ */
const $ = id => document.getElementById(id);
const val = id => ($( id ) ? $( id ).value.trim() : '');
const radioVal = name => { const el = document.querySelector(`input[name="${name}"]:checked`); return el ? el.value : ''; };

// Mode switch
function switchMode(mode) {
  document.querySelectorAll('.mode-tab').forEach((t,i) => t.classList.remove('active'));
  document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
  if (mode === 'deposit') {
    document.querySelectorAll('.mode-tab')[0].classList.add('active');
    $('panel-deposit').classList.add('active');
  } else {
    document.querySelectorAll('.mode-tab')[1].classList.add('active');
    $('panel-withdrawal').classList.add('active');
  }
}

// Accordion toggle
function toggleAcc(id) {
  const el = $(id);
  el.classList.toggle('open');
}

// Subscription mode toggle
function toggleSubMode() {
  const isSub = document.querySelector('input[name="depMode"]:checked')?.value === 'subscription';
  $('planIdField').style.display = isSub ? '' : 'none';
}

// user_token change → show/hide user_token_id and UPO field
function onUserTokenChange() {
  const v = radioVal('userToken');
  const hasToken = v !== '';
  $('userTokenIdField').style.display = hasToken ? '' : 'none';
  $('upoField').style.display = (v === 'readonlyupo') ? '' : 'none';
}
document.querySelectorAll('input[name="userToken"]').forEach(r => {
  r.addEventListener('change', onUserTokenChange);
});
onUserTokenChange(); // run on load

// Env label
document.querySelectorAll('input[name="env"]').forEach(r => {
  r.addEventListener('change', () => {
    const prod = r.value === 'prod' && r.checked;
    $('envLabel').textContent = prod ? 'PROD' : 'Test';
    $('envLabel').className = 'env-indicator ' + (prod ? 'env-prod' : 'env-test');
  });
});

/* ── Items ── */
let itemCount = 0;
function addItem() {
  itemCount++;
  const n = itemCount;
  const div = document.createElement('div');
  div.className = 'item-row';
  div.id = `item-${n}`;
  div.innerHTML = `
    <div class="item-row-header">
      <span class="item-num">Item #${n}</span>
      <button class="btn btn-danger btn-sm" onclick="removeItem(${n})">Remove</button>
    </div>
    <div class="grid grid-3">
      <div class="field">
        <label>item_name_${n} <span class="req">✱</span></label>
        <input type="text" id="iname_${n}" placeholder="Item name" value="Item name">
      </div>
      <div class="field">
        <label>item_amount_${n} <span class="req">✱</span></label>
        <input type="number" id="iamt_${n}" placeholder="0.00" step="0.01" oninput="updateTotal()">
      </div>
      <div class="field">
        <label>item_quantity_${n} <span class="req">✱</span></label>
        <input type="number" id="iqty_${n}" placeholder="1" value="1" min="1" oninput="updateTotal()">
      </div>
      <div class="field">
        <label>item_type_${n}</label>
        <select id="itype_${n}">
          <option value="">— not set —</option>
          <option value="Product">Product</option>
          <option value="Physical">Physical</option>
          <option value="Digital">Digital</option>
          <option value="Gift_card">Gift card</option>
          <option value="Store_credit">Store credit</option>
          <option value="Discount">Discount</option>
          <option value="Surcharge">Surcharge</option>
        </select>
      </div>
      <div class="field">
        <label>item_discount_${n}</label>
        <input type="number" id="idisc_${n}" placeholder="0.00" step="0.01">
      </div>
      <div class="field">
        <label>item_tax_${n} <span class="hint">%</span></label>
        <input type="number" id="itax_${n}" placeholder="0" step="0.01">
      </div>
    </div>
    <div class="grid grid-2" style="margin-top:10px;">
      <div class="field">
        <label>item_open_amount_${n}</label>
        <div class="radio-group">
          <label class="radio-pill"><input type="radio" name="iopen_${n}" value=""> — not set —</label>
          <label class="radio-pill"><input type="radio" name="iopen_${n}" value="true"> true</label>
          <label class="radio-pill"><input type="radio" name="iopen_${n}" value="false"> false</label>
        </div>
      </div>
      <div class="field">
        <label>item_min_amount_${n}</label>
        <input type="number" id="imin_${n}" placeholder="Min amount (if open)" step="0.01">
      </div>
      <div class="field">
        <label>item_max_amount_${n}</label>
        <input type="number" id="imax_${n}" placeholder="Max amount (if open)" step="0.01">
      </div>
      <div class="field">
        <label>item_shipping_${n}</label>
        <input type="number" id="iship_${n}" placeholder="0.00" step="0.01">
      </div>
      <div class="field">
        <label>item_handling_${n}</label>
        <input type="number" id="ihand_${n}" placeholder="0.00" step="0.01">
      </div>
      <div class="field">
        <label>item_number_${n} <span class="hint">serial number</span></label>
        <input type="text" id="inum_${n}" placeholder="SKU or serial number">
      </div>
      <div class="field">
        <label>item_short_description_${n}</label>
        <input type="text" id="ishortdesc_${n}" placeholder="Short description">
      </div>
      <div class="field full">
        <label>item_long_description_${n}</label>
        <input type="text" id="ilongdesc_${n}" placeholder="Long description">
      </div>
      <div class="field">
        <label>item_image_url_${n}</label>
        <input type="text" id="iimgurl_${n}" placeholder="https://...">
      </div>
      <div class="field">
        <label>item_description_url_${n}</label>
        <input type="text" id="idescurl_${n}" placeholder="https://...">
      </div>
      <div class="field">
        <label>group_id_${n} <span class="hint">food, pharmacy…</span></label>
        <input type="text" id="igroupid_${n}" placeholder="Category">
      </div>
      <div class="field">
        <label>item_tax_included_${n}</label>
        <div class="radio-group">
          <label class="radio-pill"><input type="radio" name="itaxinc_${n}" value=""> — not set —</label>
          <label class="radio-pill"><input type="radio" name="itaxinc_${n}" value="1"> 1 (included)</label>
          <label class="radio-pill"><input type="radio" name="itaxinc_${n}" value="0"> 0 (excluded)</label>
        </div>
      </div>
    </div>`;
  $('itemsContainer').appendChild(div);
  updateTotal();
}

function removeItem(n) {
  const el = $(`item-${n}`);
  if (el) el.remove();
  updateTotal();
}

function updateTotal() {
  let tot = 0;
  for (let i = 1; i <= itemCount; i++) {
    const amtEl = $(`iamt_${i}`); const qtyEl = $(`iqty_${i}`);
    if (!amtEl) continue;
    const amt = parseFloat(amtEl.value) || 0;
    const qty = parseFloat(qtyEl?.value) || 1;
    tot += amt * qty;
  }
  const discount = parseFloat(val('discount')) || 0;
  tot -= discount;
  $('totalDisplay').textContent = tot.toFixed(2);
  if (!$('totalAmount').value) { /* leave override blank */ }
}

/* ── Custom Fields ── */
let cfCount = 0;
function addCustomField() {
  cfCount++;
  const n = cfCount <= 15 ? cfCount : cfCount; // allow up to 15
  const div = document.createElement('div');
  div.className = 'kv-row';
  div.id = `cf-row-${n}`;
  div.innerHTML = `
    <select style="width:160px; flex:none;" id="cfKey_${n}">
      ${[...Array(15)].map((_,i)=>`<option value="customField${i+1}">customField${i+1}</option>`).join('')}
    </select>
    <input type="text" id="cfVal_${n}" placeholder="Value (max 64 chars)" maxlength="64">
    <button class="btn btn-danger btn-sm" onclick="this.parentElement.remove()">✕</button>`;
  $('customFieldsContainer').appendChild(div);
}

let wdCfCount = 0;
function addWdCustomField() {
  wdCfCount++;
  const n = wdCfCount;
  const div = document.createElement('div');
  div.className = 'kv-row';
  div.innerHTML = `
    <select style="width:160px; flex:none;" id="wdCfKey_${n}">
      ${[...Array(15)].map((_,i)=>`<option value="customField${i+1}">customField${i+1}</option>`).join('')}
    </select>
    <input type="text" id="wdCfVal_${n}" placeholder="Value (max 64 chars)" maxlength="64">
    <button class="btn btn-danger btn-sm" onclick="this.parentElement.remove()">✕</button>`;
  $('wdCustomFieldsContainer').appendChild(div);
}

/* ── Free-form params ── */
let ffCount = 0;
function addFreeFormParam(containerId) {
  ffCount++;
  const n = ffCount;
  const div = document.createElement('div');
  div.className = 'kv-row';
  div.innerHTML = `
    <input type="text" id="ffKey_${n}" placeholder="parameter_name" style="font-family:var(--mono);font-size:12px;">
    <input type="text" id="ffVal_${n}" placeholder="value">
    <button class="btn btn-danger btn-sm" onclick="this.parentElement.remove()">✕</button>`;
  $(containerId).appendChild(div);
}

/* ── Timestamp helpers ── */
function setCurrentTimestamp(mode) {
  const now = new Date();
  const pad = n => String(n).padStart(2,'0');
  if (mode === 'dep') {
    // YYYY-MM-DD.HH:MM:SS
    const ts = `${now.getUTCFullYear()}-${pad(now.getUTCMonth()+1)}-${pad(now.getUTCDate())}.${pad(now.getUTCHours())}:${pad(now.getUTCMinutes())}:${pad(now.getUTCSeconds())}`;
    $('depTimestamp').value = ts;
  } else {
    // YYYYMMDDHHMMSS
    const ts = `${now.getUTCFullYear()}${pad(now.getUTCMonth()+1)}${pad(now.getUTCDate())}${pad(now.getUTCHours())}${pad(now.getUTCMinutes())}${pad(now.getUTCSeconds())}`;
    $('wdTimestamp').value = ts;
  }
}

/* ── Base URLs ── */
function getBaseUrl(mode) {
  const env = radioVal('env');
  const company = radioVal('company');
  const prod = env === 'prod';
  const host = prod
    ? (company === 'nuvei' ? 'https://secure.nuvei.com' : 'https://secure.safecharge.com')
    : (company === 'nuvei' ? 'https://ppp-test.nuvei.com' : 'https://ppp-test.safecharge.com');
  if (mode === 'withdrawal') return `${host}/ppp/withdrawal/withdraw.do`;
  return `${host}/ppp/purchase.do`;
}

/* ══════════════════════════════════════════
   HASHING (MD5 / SHA-256 in browser)
══════════════════════════════════════════ */
async function sha256(str) {
  const buf = await crypto.subtle.digest('SHA-256', new TextEncoder().encode(str));
  return Array.from(new Uint8Array(buf)).map(b=>b.toString(16).padStart(2,'0')).join('');
}

function md5(str) {
  // RFC 1321 MD5 implementation
  function safeAdd(x,y){const lsw=(x&0xFFFF)+(y&0xFFFF);const msw=(x>>16)+(y>>16)+(lsw>>16);return(msw<<16)|(lsw&0xFFFF);}
  function bitRotateLeft(num,cnt){return(num<<cnt)|(num>>>(32-cnt));}
  function md5cmn(q,a,b,x,s,t){return safeAdd(bitRotateLeft(safeAdd(safeAdd(a,q),safeAdd(x,t)),s),b);}
  function md5ff(a,b,c,d,x,s,t){return md5cmn((b&c)|((~b)&d),a,b,x,s,t);}
  function md5gg(a,b,c,d,x,s,t){return md5cmn((b&d)|(c&(~d)),a,b,x,s,t);}
  function md5hh(a,b,c,d,x,s,t){return md5cmn(b^c^d,a,b,x,s,t);}
  function md5ii(a,b,c,d,x,s,t){return md5cmn(c^(b|(~d)),a,b,x,s,t);}
  const m=[];let i;const length8=str.length*8;
  for(i=0;i<str.length;i+=4){m[i>>2]=str.charCodeAt(i)+(str.charCodeAt(i+1)<<8)+(str.charCodeAt(i+2)<<16)+(str.charCodeAt(i+3)<<24);}
  m[str.length>>2]|=0x80<<((str.length%4)*8);m[(((str.length+8)>>6)<<4)+14]=length8;
  let a=1732584193,b=-271733879,c=-1732584194,d=271733878;
  for(i=0;i<m.length;i+=16){
    const olda=a,oldb=b,oldc=c,oldd=d;
    a=md5ff(a,b,c,d,m[i],7,-680876936);d=md5ff(d,a,b,c,m[i+1],12,-389564586);c=md5ff(c,d,a,b,m[i+2],17,606105819);b=md5ff(b,c,d,a,m[i+3],22,-1044525330);
    a=md5ff(a,b,c,d,m[i+4],7,-176418897);d=md5ff(d,a,b,c,m[i+5],12,1200080426);c=md5ff(c,d,a,b,m[i+6],17,-1473231341);b=md5ff(b,c,d,a,m[i+7],22,-45705983);
    a=md5ff(a,b,c,d,m[i+8],7,1770035416);d=md5ff(d,a,b,c,m[i+9],12,-1958414417);c=md5ff(c,d,a,b,m[i+10],17,-42063);b=md5ff(b,c,d,a,m[i+11],22,-1990404162);
    a=md5ff(a,b,c,d,m[i+12],7,1804603682);d=md5ff(d,a,b,c,m[i+13],12,-40341101);c=md5ff(c,d,a,b,m[i+14],17,-1502002290);b=md5ff(b,c,d,a,m[i+15],22,1236535329);
    a=md5gg(a,b,c,d,m[i+1],5,-165796510);d=md5gg(d,a,b,c,m[i+6],9,-1069501632);c=md5gg(c,d,a,b,m[i+11],14,643717713);b=md5gg(b,c,d,a,m[i],20,-373897302);
    a=md5gg(a,b,c,d,m[i+5],5,-701558691);d=md5gg(d,a,b,c,m[i+10],9,38016083);c=md5gg(c,d,a,b,m[i+15],14,-660478335);b=md5gg(b,c,d,a,m[i+4],20,-405537848);
    a=md5gg(a,b,c,d,m[i+9],5,568446438);d=md5gg(d,a,b,c,m[i+14],9,-1019803690);c=md5gg(c,d,a,b,m[i+3],14,-187363961);b=md5gg(b,c,d,a,m[i+8],20,1163531501);
    a=md5gg(a,b,c,d,m[i+13],5,-1444681467);d=md5gg(d,a,b,c,m[i+2],9,-51403784);c=md5gg(c,d,a,b,m[i+7],14,1735328473);b=md5gg(b,c,d,a,m[i+12],20,-1926607734);
    a=md5hh(a,b,c,d,m[i+5],4,-378558);d=md5hh(d,a,b,c,m[i+8],11,-2022574463);c=md5hh(c,d,a,b,m[i+11],16,1839030562);b=md5hh(b,c,d,a,m[i+14],23,-35309556);
    a=md5hh(a,b,c,d,m[i+1],4,-1530992060);d=md5hh(d,a,b,c,m[i+4],11,1272893353);c=md5hh(c,d,a,b,m[i+7],16,-155497632);b=md5hh(b,c,d,a,m[i+10],23,-1094730640);
    a=md5hh(a,b,c,d,m[i+13],4,681279174);d=md5hh(d,a,b,c,m[i],11,-358537222);c=md5hh(c,d,a,b,m[i+3],16,-722521979);b=md5hh(b,c,d,a,m[i+6],23,76029189);
    a=md5hh(a,b,c,d,m[i+9],4,-640364487);d=md5hh(d,a,b,c,m[i+12],11,-421815835);c=md5hh(c,d,a,b,m[i+15],16,530742520);b=md5hh(b,c,d,a,m[i+2],23,-995338651);
    a=md5ii(a,b,c,d,m[i],6,-198630844);d=md5ii(d,a,b,c,m[i+7],10,1126891415);c=md5ii(c,d,a,b,m[i+14],15,-1416354905);b=md5ii(b,c,d,a,m[i+5],21,-57434055);
    a=md5ii(a,b,c,d,m[i+12],6,1700485571);d=md5ii(d,a,b,c,m[i+3],10,-1894986606);c=md5ii(c,d,a,b,m[i+10],15,-1051523);b=md5ii(b,c,d,a,m[i+1],21,-2054922799);
    a=md5ii(a,b,c,d,m[i+8],6,1873313359);d=md5ii(d,a,b,c,m[i+15],10,-30611744);c=md5ii(c,d,a,b,m[i+6],15,-1560198380);b=md5ii(b,c,d,a,m[i+13],21,1309151649);
    a=md5ii(a,b,c,d,m[i+4],6,-145523070);d=md5ii(d,a,b,c,m[i+11],10,-1120210379);c=md5ii(c,d,a,b,m[i+2],15,718787259);b=md5ii(b,c,d,a,m[i+9],21,-343485551);
    a=safeAdd(a,olda);b=safeAdd(b,oldb);c=safeAdd(c,oldc);d=safeAdd(d,oldd);
  }
  function hex(n){let s='';for(let i=0;i<4;i++){s+=('0'+(((n>>>(i*8))&0xFF).toString(16))).slice(-2);}return s;}
  return hex(a)+hex(b)+hex(c)+hex(d);
}

async function hashString(str, algo) {
  if (algo === 'MD5') return md5(str);
  return await sha256(str);
}


/* ══════════════════════════════════════════
   DEPOSIT CALCULATE
══════════════════════════════════════════ */
async function calculateDeposit() {
  const secret = val('secretKey');
  if (!secret) { alert('Please enter your Secret Key.'); return; }

  const env = radioVal('env');
  const company = radioVal('company');
  const algo = radioVal('hashAlgo');
  const version = radioVal('version');
  const depMode = radioVal('depMode');

  if (depMode === 'subscription' && !radioVal('userToken')) {
    alert('Subscription mode requires user_token to be set (use "auto" for new customers).');
    return;
  }

  const params = Object.create(null);
  const add = (k, v) => {
    if (v !== null && v !== undefined && String(v).trim() !== '') params[k] = String(v);
  };
  const addFields = fields => fields.forEach(([key, id]) => add(key, val(id)));

  add('merchant_id', val('merchantId'));
  add('merchant_site_id', val('merchantSiteId'));

  const ut = radioVal('userToken');
  if (ut) {
    add('user_token', ut);
    add('user_token_id', val('userTokenId'));
    if (ut === 'readonlyupo') add('userPaymentOptionId', val('userPaymentOptionId'));
  }

  if (depMode === 'subscription') add('rebillingPlanId', val('planId'));
  add('userid', val('userId'));

  const itemDivs = $('itemsContainer').querySelectorAll('.item-row');
  const numItems = itemDivs.length;
  let itemIdx = 0;

  itemDivs.forEach(div => {
    const n = div.id.replace('item-','');
    itemIdx++;

    add(`item_name_${itemIdx}`, val(`iname_${n}`));
    add(`item_number_${itemIdx}`, val(`inum_${n}`));
    const itype = val(`itype_${n}`); if (itype) add(`item_type_${itemIdx}`, itype);
    add(`item_amount_${itemIdx}`, val(`iamt_${n}`));
    add(`item_quantity_${itemIdx}`, val(`iqty_${n}`) || '1');
    add(`item_discount_${itemIdx}`, val(`idisc_${n}`));
    add(`item_shipping_${itemIdx}`, val(`iship_${n}`));
    add(`item_handling_${itemIdx}`, val(`ihand_${n}`));
    add(`item_tax_${itemIdx}`, val(`itax_${n}`));
    add(`item_short_description_${itemIdx}`, val(`ishortdesc_${n}`));
    add(`item_long_description_${itemIdx}`, val(`ilongdesc_${n}`));
    add(`item_image_url_${itemIdx}`, val(`iimgurl_${n}`));
    add(`item_description_url_${itemIdx}`, val(`idescurl_${n}`));
    add(`group_id_${itemIdx}`, val(`igroupid_${n}`));
    add(`item_tax_included_${itemIdx}`, radioVal(`itaxinc_${n}`));
    add(`item_open_amount_${itemIdx}`, radioVal(`iopen_${n}`));
    add(`item_min_amount_${itemIdx}`, val(`imin_${n}`));
    add(`item_max_amount_${itemIdx}`, val(`imax_${n}`));
  });

  if (numItems > 1) add('numberofitems', String(numItems));

  const totalOverride = val('totalAmount');
  const autoTotal = $('totalDisplay').textContent;
  add('total_amount', totalOverride || autoTotal);

  add('currency', val('currency').toUpperCase());
  add('version', version || '4.0.0');
  add('time_stamp', val('depTimestamp'));

  add('encoding', radioVal('encoding'));
  add('merchantLocale', val('merchantLocale'));
  add('payment_method', val('paymentMethod'));
  add('payment_method_mode', radioVal('pmMode'));
  add('theme_id', val('themeId'));
  add('invoice_id', val('invoiceId'));
  add('merchant_unique_id', val('merchantUniqueId'));
  add('discount', val('discount'));
  add('customData', val('customData'));
  add('webMasterId', val('webMasterId'));
  add('productId', val('productId'));
  add('promoCode', val('promoCode'));
  add('current_balance', val('currentBalance'));
  add('total_tax', val('totalTax'));
  add('shipping', val('shipping'));
  add('handling', val('handling'));
  add('allow_installments', radioVal('allowInst'));
  add('max_num_installments', val('maxInstallments'));
  add('processingChannel', radioVal('procChannel'));
  add('quick_deposit', radioVal('quickDep'));
  add('pm_hints', radioVal('pmHints'));
  add('isNative', radioVal('isNative'));

  addFields([
    ['suggestedAmount1', 'sugAmt1'],
    ['suggestedAmount2', 'sugAmt2'],
    ['suggestedAmount3', 'sugAmt3'],
    ['email', 'cEmail'],
    ['country', 'cCountry'],
    ['first_name', 'cFirstName'],
    ['last_name', 'cLastName'],
    ['title', 'cTitle'],
    ['phone1', 'cPhone1'],
    ['phone2', 'cPhone2'],
    ['address1', 'cAddress1'],
    ['address2', 'cAddress2'],
    ['city', 'cCity'],
    ['state', 'cState'],
    ['zip', 'cZip'],
    ['personal_id', 'cPersonalId'],
    ['dateOfBirth', 'cDateOfBirth'],
    ['currentNumberOfTransactions', 'cTxCount'],
    ['customerSince', 'cSince'],
    ['success_url', 'successUrl'],
    ['error_url', 'errorUrl'],
    ['pending_url', 'pendingUrl'],
    ['notify_url', 'notifyUrl'],
    ['back_url', 'backUrl'],
    ['appUrl', 'appUrl'],
    ['shippingFirstName', 'shipFirstName'],
    ['shippingLastName', 'shipLastName'],
    ['shippingAddress', 'shipAddress'],
    ['shippingCity', 'shipCity'],
    ['shippingCountry', 'shipCountry'],
    ['shippingZip', 'shipZip'],
    ['shippingState', 'shipState'],
    ['shippingCompanyName', 'shipCompany'],
    ['shippingStreetNumber', 'shipStreetNum'],
    ['AddressType', 'shipAddressType'],
    ['shipIndicator', 'shipIndicator'],
    ['deliveryEmail', 'deliveryEmail'],
    ['deliveryTimeFrame', 'deliveryTimeFrame'],
    ['preOrderDate', 'preOrderDate'],
    ['preOrderInd', 'preOrderInd'],
    ['reorderItemsInd', 'reorderItemsInd'],
    ['threeDSChallengeWindowSize', 'threeDSChallengeWindowSize'],
    ['accountAge', 'accountAge'],
    ['accountLastChangeDate', 'accountLastChangeDate'],
    ['accountLastChangeInd', 'accountLastChangeInd'],
    ['accountRegistrationDate', 'accountRegistrationDate'],
    ['accountPasswordChangeDate', 'accountPasswordChangeDate'],
    ['accountResetInd', 'accountResetInd'],
    ['accountPurchasesCount6M', 'accountPurchasesCount6M'],
    ['accountAddCardAttempts24H', 'accountAddCardAttempts24H'],
    ['accountTransactionsCount24H', 'accountTransactionsCount24H'],
    ['accountTransactionsCount1Y', 'accountTransactionsCount1Y'],
    ['accountAddressFirstUseDate', 'accountAddressFirstUseDate'],
    ['accountAddressFirstUseInd', 'accountAddressFirstUseInd'],
    ['accountNameInd', 'accountNameInd'],
    ['accountSuspiciousActivityInd', 'accountSuspiciousActivityInd'],
    ['giftCardAmount', 'giftCardAmount'],
    ['giftCardCount', 'giftCardCount'],
    ['giftCardCurrency', 'giftCardCurrency'],
    ['descriptorMerchantName', 'descName'],
    ['descriptorMerchantPhone', 'descPhone'],
    ['customSiteName', 'customSiteName'],
    ['creditCardDepositAmount', 'ccDepAmt'],
    ['MaxCreditCardDepositAmount', 'ccMaxDepAmt'],
    ['withdrawableBalance', 'withdrawableBalance'],
    ['digitalAssetType', 'digitalAssetType']
  ]);

  add('existingCustomer', radioVal('existCust'));
  add('skip_review_tab', radioVal('skipReview'));
  add('skip_billing_tab', radioVal('skipBilling'));

  document.querySelectorAll('#customFieldsContainer .kv-row').forEach(row => {
    const key = row.querySelector('select')?.value;
    const value = row.querySelector('input')?.value;
    add(key, value);
  });

  document.querySelectorAll('#freeFormContainer .kv-row').forEach(row => {
    const inputs = row.querySelectorAll('input');
    if (inputs[0]?.value.trim()) add(inputs[0].value.trim(), inputs[1]?.value);
  });

  const preHash = secret + Object.values(params).join('');

  const checksum = await hashString(preHash, algo);

  params['checksum'] = checksum;

  const base = getBaseUrl('deposit');
  const qs = Object.entries(params)
    .map(([k,v]) => `${encodeURIComponent(k)}=${encodeURIComponent(v)}`)
    .join('&');

  const fullUrl = `${base}?${qs}`;

  $('depPreHash').textContent = preHash;
  $('depChecksum').textContent = checksum;
  $('depUrl').textContent = fullUrl;
  $('depositResult').style.display = '';
}

/* ══════════════════════════════════════════
   WITHDRAWAL CALCULATE
══════════════════════════════════════════ */
async function calculateWithdrawal() {
  const secret = val('secretKey');
  if (!secret) { alert('Please enter your Secret Key.'); return; }
  const algo = radioVal('hashAlgo');
  const params = Object.create(null);
  const add = (k, v) => {
    if (v !== null && v !== undefined && String(v).trim() !== '') params[k] = String(v);
  };

  add('merchant_id', val('merchantId'));
  add('merchant_site_id', val('merchantSiteId'));
  const wut = radioVal('wdUserToken'); if (wut) add('user_token', wut);
  add('user_token_id', val('wdUserTokenId'));
  const wdLocale = val('wdLocale'); if (wdLocale) add('merchantLocale', wdLocale);
  add('wd_amount', val('wdAmount'));
  add('wd_currency', val('wdCurrency').toUpperCase());
  const wdCountry = val('wdCountry'); if (wdCountry) add('country', wdCountry);
  const wdoa = radioVal('wdOpenAmt'); if (wdoa) add('wd_open_amount', wdoa);
  add('wd_min_amount', val('wdMinAmount'));
  add('wd_max_amount', val('wdMaxAmount'));
  add('userId', val('wdUserId'));
  add('timeStamp', val('wdTimestamp'));
  add('customSiteName', val('wdCustomSiteName'));

  // WD custom fields
  document.querySelectorAll('[id^="wdCfKey_"]').forEach(el => {
    const n = el.id.split('_')[1];
    const k = el.value;
    const v = val(`wdCfVal_${n}`);
    if (k && v) add(k, v);
  });

  add('merchant_unique_id', val('wdMerchantUniqueId'));
  add('version', '4.0.0');

  // URLs
  add('successUrl', val('wdSuccessUrl'));
  add('failUrl', val('wdFailUrl'));
  add('backUrl', val('wdBackUrl'));

  const wdscb = radioVal('wdShowCancel'); if (wdscb) add('showCancelButton', wdscb);
  const wdl = val('wdLayout'); if (wdl) add('layout', wdl);
  add('iban', val('wdIban'));
  const wddat = val('wdDigitalAssetType'); if (wddat) add('digitalAssetType', wddat);

  // Free-form
  document.querySelectorAll('#wdFreeFormContainer .kv-row').forEach(row => {
    const inputs = row.querySelectorAll('input');
    if (inputs[0]?.value.trim() && inputs[1]?.value.trim()) {
      add(inputs[0].value.trim(), inputs[1].value.trim());
    }
  });

  // ⚠ Withdrawal: secret key goes at the FRONT
  const preHash = secret + Object.values(params).join('');
  const checksum = await hashString(preHash, algo);

  params['checksum'] = checksum;
  const base = getBaseUrl('withdrawal');
  const qs = Object.entries(params).map(([k,v]) => `${encodeURIComponent(k)}=${encodeURIComponent(v)}`).join('&');
  const fullUrl = `${base}?${qs}`;

  $('wdPreHash').textContent = preHash;
  $('wdChecksum').textContent = checksum;
  $('wdUrl').textContent = fullUrl;
  $('withdrawalResult').style.display = '';
  $('withdrawalResult').scrollIntoView({ behavior: 'smooth' });
}

/* ── Helpers ── */
function copyResult(id) {
  const text = $(id).textContent;
  navigator.clipboard.writeText(text).then(() => {
    const el = $(id); el.classList.add('copied');
    setTimeout(() => el.classList.remove('copied'), 1500);
  });
}

function openUrl(id) {
  window.open($(id).textContent, '_blank');
}

function openInIframe(urlId) {
  const url = $(urlId).textContent;
  $('pppIframe').src = url;
  $('iframeContainer').style.display = '';
  $('pppIframe').width = $('iframeW').value || 800;
  $('pppIframe').height = $('iframeH').value || 600;
  $('iframeContainer').scrollIntoView({ behavior: 'smooth' });
  $('iframeContainer').style.display = 'block';
}

function resizeIframe() {
  $('pppIframe').width = $('iframeW').value;
  $('pppIframe').height = $('iframeH').value;
}

function closeIframe() {
  $('iframeContainer').style.display = 'none';
  $('pppIframe').src = '';
}

function toggleCardGenerator() {
  const isOpen = $('cardGeneratorContainer').style.display === 'block';
  setCardGeneratorOpen(!isOpen);
}

function closeCardGenerator() {
  setCardGeneratorOpen(false);
}

function setCardGeneratorOpen(isOpen) {
  const container = $('cardGeneratorContainer');
  const iframe = $('cardGeneratorIframe');
  const toggle = $('cardGeneratorToggle');

  if (isOpen && !iframe.dataset.loaded) {
    iframe.src = 'https://stefanovweb.eu/cardgen/';
    iframe.dataset.loaded = 'true';
  }

  container.style.display = isOpen ? 'block' : 'none';
  toggle.setAttribute('aria-expanded', String(isOpen));
  toggle.textContent = isOpen ? 'Hide Card Generator' : 'Open Card Generator';

  if (isOpen) container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function clearDeposit() {
  document.querySelectorAll('#panel-deposit input[type="text"], #panel-deposit input[type="number"]').forEach(e => e.value = '');
  $('itemsContainer').innerHTML = '';
  itemCount = 0;
  $('customFieldsContainer').innerHTML = '';
  $('freeFormContainer').innerHTML = '';
  $('depositResult').style.display = 'none';
  $('totalDisplay').textContent = '0.00';
}

function clearWithdrawal() {
  document.querySelectorAll('#panel-withdrawal input[type="text"], #panel-withdrawal input[type="number"]').forEach(e => e.value = '');
  $('wdCustomFieldsContainer').innerHTML = '';
  $('wdFreeFormContainer').innerHTML = '';
  $('withdrawalResult').style.display = 'none';
}

// Init: add first item by default
addItem();
