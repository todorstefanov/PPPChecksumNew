<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nuvei PPP Checksum Calculator v2</title>
<script src="functions.js"></script>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header>
  <div class="logo">N</div>
  <h1>Nuvei PPP Checksum Calculator</h1>
  <span id="envLabel" class="env-indicator env-test">Test</span>
</header>

<div class="container">

  <!-- ── ACCOUNT INFO CARD (shared) ── -->
  <div class="card">
    <div class="card-title">Account Info <span class="badge badge-req">Required for all modes</span></div>
    <div class="grid grid-3">
      <div class="field">
        <label>Environment <span class="req">✱</span></label>
        <div class="radio-group">
          <label class="radio-pill"><input type="radio" name="env" value="prod"> Production</label>
          <label class="radio-pill"><input type="radio" name="env" value="test" checked> Sandbox</label>
        </div>
      </div>
      <div class="field">
        <label>Domain <span class="req">✱</span></label>
        <div class="radio-group">
          <label class="radio-pill"><input type="radio" name="company" value="safecharge"> safecharge.com</label>
          <label class="radio-pill"><input type="radio" name="company" value="nuvei" checked> nuvei.com</label>
        </div>
      </div>
      <div class="field">
        <label>Secret Key <span class="req">✱</span></label>
        <input type="text" id="secretKey" placeholder="Your merchant secret key">
      </div>
      <div class="field">
        <label>Merchant ID <span class="req">✱</span></label>
        <input type="text" id="merchantId" placeholder="e.g. 5121528327976171922">
      </div>
      <div class="field">
        <label>Merchant Site ID <span class="req">✱</span></label>
        <input type="text" id="merchantSiteId" placeholder="e.g. 137723">
      </div>
      <div class="field">
        <label>Hash Algorithm <span class="req">✱</span></label>
        <div class="radio-group">
          <label class="radio-pill"><input type="radio" name="hashAlgo" value="MD5"> MD5</label>
          <label class="radio-pill"><input type="radio" name="hashAlgo" value="SHA256" checked> SHA-256</label>
        </div>
      </div>
    </div>
  </div>

  <!-- ── MODE TABS ── -->
  <div class="mode-tabs">
    <button class="mode-tab active" onclick="switchMode('deposit')">💳 Deposit / Subscription</button>
    <button class="mode-tab" onclick="switchMode('withdrawal')">🏦 Withdrawal</button>
  </div>

  <!-- ══════════════════════════════════════════════
       DEPOSIT + SUBSCRIPTION PANEL
  ══════════════════════════════════════════════ -->
  <div id="panel-deposit" class="panel active">

    <!-- Subscription toggle notice -->
    <div class="card" style="border-color: rgba(245,158,11,.3);">
      <div class="card-title">Mode <span class="badge badge-opt">Optional</span></div>
      <div class="grid grid-2">
        <div class="field">
          <label>Transaction Type</label>
          <div class="radio-group">
            <label class="radio-pill"><input type="radio" name="depMode" value="deposit" checked onchange="toggleSubMode()"> Standard Deposit</label>
            <label class="radio-pill"><input type="radio" name="depMode" value="subscription" onchange="toggleSubMode()"> Subscription (Rebilling)</label>
          </div>
        </div>
        <div class="field" id="planIdField" style="display:none;">
          <label class="req">✱</label>
          <label>Plan ID <span class="req">required for subscription</span></label>
          <input type="text" id="planId" placeholder="Rebilling Plan ID from Nuvei Control Panel">
        </div>
      </div>
    </div>

    <!-- User Token -->
    <div class="card">
      <div class="card-title">User Token <span class="badge badge-opt">Optional for Deposit</span></div>
      <div class="grid grid-2">
        <div class="field">
          <label>user_token <span class="hint">omit for standard deposit without token</span></label>
          <div class="radio-group">
            <label class="radio-pill"><input type="radio" name="userToken" value="" checked> — not used —</label>
            <label class="radio-pill"><input type="radio" name="userToken" value="auto"> auto</label>
            <label class="radio-pill"><input type="radio" name="userToken" value="register"> register</label>
            <label class="radio-pill"><input type="radio" name="userToken" value="readonly"> readOnly</label>
            <label class="radio-pill"><input type="radio" name="userToken" value="registeronly"> RegisterOnly</label>
            <label class="radio-pill"><input type="radio" name="userToken" value="readonlyupo"> readonlyupo</label>
          </div>
        </div>
        <div class="field" id="userTokenIdField" style="display:none;">
          <label>user_token_id <span class="hint">Char(255) — required when user_token is set</span></label>
          <input type="text" id="userTokenId" placeholder="Unique user ID in your system">
        </div>
        <div class="field" id="upoField" style="display:none;">
          <label>userPaymentOptionId <span class="hint">for readonlyupo</span></label>
          <input type="text" id="userPaymentOptionId" placeholder="UPO ID">
        </div>
        <div class="field">
          <label>userid <span class="hint">Char(50)</span></label>
          <input type="text" id="userId" placeholder="Customer ID in merchant DB">
        </div>
      </div>
    </div>

    <!-- Items -->
    <div class="card">
      <div class="card-title">
        Item Details <span class="badge badge-req">Required</span>
        <button class="btn btn-secondary btn-sm" onclick="addItem()" style="margin-left:auto;">+ Add Item</button>
      </div>
      <div id="itemsContainer"></div>
      <div class="amount-row">
        <span class="amount-label">Total Amount (auto-calculated)</span>
        <span class="amount-value" id="totalDisplay">0.00</span>
      </div>
      <div class="grid grid-2" style="margin-top:12px;">
        <div class="field">
          <label>total_amount override <span class="hint">leave blank to auto-calculate</span></label>
          <input type="number" id="totalAmount" placeholder="e.g. 10.00" step="0.01" oninput="updateTotal()">
        </div>
        <div class="field">
          <label>currency <span class="req">✱</span> <span class="hint">ISO 4217</span></label>
          <input type="text" id="currency" value="EUR" placeholder="EUR" maxlength="3" style="text-transform:uppercase;">
        </div>
        <div class="field">
          <label>discount <span class="hint">additional discount</span></label>
          <input type="number" id="discount" placeholder="0.00" step="0.01" oninput="updateTotal()">
        </div>
        <div class="field">
          <label>total_tax <span class="hint">VAT %</span></label>
          <input type="number" id="totalTax" placeholder="0" step="0.01">
        </div>
        <div class="field">
          <label>shipping <span class="hint">total shipping</span></label>
          <input type="number" id="shipping" placeholder="0.00" step="0.01">
        </div>
        <div class="field">
          <label>handling <span class="hint">total handling</span></label>
          <input type="number" id="handling" placeholder="0.00" step="0.01">
        </div>
      </div>
    </div>

    <!-- Basic Optional -->
    <div class="card">
      <div class="card-title">Basic Parameters <span class="badge badge-opt">Optional</span></div>
      <div class="grid grid-2">
        <div class="field">
          <label>version <span class="req">✱</span></label>
          <div class="radio-group">
            <label class="radio-pill"><input type="radio" name="version" value="3.0.0"> 3.0.0</label>
            <label class="radio-pill"><input type="radio" name="version" value="4.0.0" checked> 4.0.0</label>
          </div>
        </div>
        <div class="field">
          <label>encoding</label>
          <div class="radio-group">
            <label class="radio-pill"><input type="radio" name="encoding" value="" checked> ISO-8859-1</label>
            <label class="radio-pill"><input type="radio" name="encoding" value="UTF-8"> UTF-8</label>
          </div>
        </div>
        <div class="field">
          <label>merchantLocale <span class="hint">e.g. en_US</span></label>
          <select id="merchantLocale">
            <option value="">— not set —</option>
            <option value="en_US">English (US) – en_US</option>
            <option value="en_UK">English (UK) – en_UK</option>
            <option value="en_AU">English (AU) – en_AU</option>
            <option value="bg_BG">Bulgarian – bg_BG</option>
            <option value="de_DE">German – de_DE</option>
            <option value="fr_FR">French – fr_FR</option>
            <option value="es_ES">Spanish – es_ES</option>
            <option value="it_IT">Italian – it_IT</option>
            <option value="ru_RU">Russian – ru_RU</option>
            <option value="ar_AA">Arabic – ar_AA</option>
            <option value="zh_CN">Chinese (PRC) – zh_CN</option>
            <option value="ja_JP">Japanese – ja_JP</option>
            <option value="ko_KR">Korean – ko_KR</option>
            <option value="pt_BR">Portuguese – pt_BR</option>
            <option value="tr_TR">Turkish – tr_TR</option>
            <option value="pl_PL">Polish – pl_PL</option>
            <option value="nl_NL">Dutch – nl_NL</option>
            <option value="ro_RO">Romanian – ro_RO</option>
            <option value="hu_HU">Hungarian – hu_HU</option>
            <option value="cs_CS">Czech – cs_CS</option>
            <option value="hr_HR">Croatian – hr_HR</option>
            <option value="sk_SK">Slovak – sk_SK</option>
            <option value="sl_SI">Slovenian – sl_SI</option>
            <option value="uk_UK">Ukrainian – uk_UK</option>
            <option value="sq_SQ">Albanian – sq_SQ</option>
            <option value="el_EL">Greek – el_EL</option>
            <option value="iw_IL">Hebrew – iw_IL</option>
            <option value="hi_HI">Hindi – hi_HI</option>
            <option value="th_TH">Thai – th_TH</option>
            <option value="vi_VL">Vietnamese – vi_VL</option>
            <option value="sv_SE">Swedish – sv_SE</option>
            <option value="da_DK">Danish – da_DK</option>
            <option value="fi_FI">Finnish – fi_FI</option>
            <option value="no_NO">Norwegian – no_NO</option>
          </select>
        </div>
        <div class="field">
          <label>payment_method <span class="hint">filter to specific PM</span></label>
          <select id="paymentMethod">
            <option value="">— not set —</option>
            <option value="cc_card">Credit Card (cc_card)</option>
            <option value="ppp_GooglePay">Google Pay</option>
            <option value="ppp_ApplePay">Apple Pay</option>
            <option value="apmgw_Skrill">Skrill</option>
            <option value="apmgw_Neteller">Neteller</option>
            <option value="apmgw_PayPal">PayPal</option>
            <option value="apmgw_Klarna">Klarna</option>
            <option value="apmgw_Pix">PIX</option>
          </select>
        </div>
        <div class="field">
          <label>payment_method_mode</label>
          <div class="radio-group">
            <label class="radio-pill"><input type="radio" name="pmMode" value=""> — not set —</label>
            <label class="radio-pill"><input type="radio" name="pmMode" value="filter"> filter</label>
          </div>
        </div>
        <div class="field">
          <label>theme_id</label>
          <input type="text" id="themeId" placeholder="Theme ID (numeric)">
        </div>
        <div class="field">
          <label>invoice_id <span class="hint">Char(100)</span></label>
          <input type="text" id="invoiceId" placeholder="Merchant invoice ID">
        </div>
        <div class="field">
          <label>merchant_unique_id <span class="hint">Char(64)</span></label>
          <input type="text" id="merchantUniqueId" placeholder="Transaction ID in merchant DB">
        </div>
        <div class="field">
          <label>customData <span class="hint">Char(255)</span></label>
          <input type="text" id="customData" placeholder="General customer data">
        </div>
        <div class="field">
          <label>webMasterId <span class="hint">affiliate param</span></label>
          <input type="text" id="webMasterId" placeholder="Affiliate / webmaster ID">
        </div>
        <div class="field">
          <label>productId <span class="hint">Char(50)</span></label>
          <input type="text" id="productId" placeholder="Merchant product ID">
        </div>
        <div class="field">
          <label>promoCode <span class="hint">Char(512)</span></label>
          <input type="text" id="promoCode" placeholder="Promotional code">
        </div>
        <div class="field">
          <label>current_balance <span class="hint">Customer account balance</span></label>
          <input type="number" id="currentBalance" placeholder="0.00" step="0.01">
        </div>
        <div class="field">
          <label>allow_installments</label>
          <div class="radio-group">
            <label class="radio-pill"><input type="radio" name="allowInst" value=""> — not set —</label>
            <label class="radio-pill"><input type="radio" name="allowInst" value="1"> Yes (1)</label>
            <label class="radio-pill"><input type="radio" name="allowInst" value="0"> No (0)</label>
          </div>
        </div>
        <div class="field" id="maxInstField">
          <label>max_num_installments</label>
          <input type="number" id="maxInstallments" placeholder="Max installments">
        </div>
        <div class="field">
          <label>processingChannel</label>
          <div class="radio-group">
            <label class="radio-pill"><input type="radio" name="procChannel" value=""> — not set —</label>
            <label class="radio-pill"><input type="radio" name="procChannel" value="MOTO"> MOTO</label>
          </div>
        </div>
        <div class="field">
          <label>quick_deposit</label>
          <div class="radio-group">
            <label class="radio-pill"><input type="radio" name="quickDep" value=""> — not set —</label>
            <label class="radio-pill"><input type="radio" name="quickDep" value="1"> 1 (enabled)</label>
            <label class="radio-pill"><input type="radio" name="quickDep" value="0"> 0 (disabled)</label>
          </div>
        </div>
        <div class="field">
          <label>pm_hints</label>
          <div class="radio-group">
            <label class="radio-pill"><input type="radio" name="pmHints" value=""> — not set —</label>
            <label class="radio-pill"><input type="radio" name="pmHints" value="True"> True</label>
            <label class="radio-pill"><input type="radio" name="pmHints" value="False"> False</label>
          </div>
        </div>
        <div class="field">
          <label>isNative</label>
          <div class="radio-group">
            <label class="radio-pill"><input type="radio" name="isNative" value=""> — not set —</label>
            <label class="radio-pill"><input type="radio" name="isNative" value="1"> 1 (same window)</label>
            <label class="radio-pill"><input type="radio" name="isNative" value="0"> 0 (new window)</label>
          </div>
        </div>
      </div>
    </div>

    <!-- Suggested amounts -->
    <div class="accordion" id="acc-suggested">
      <div class="accordion-header" onclick="toggleAcc('acc-suggested')">
        <span class="acc-title">Suggested Amounts</span>
        <span class="acc-desc">Pre-defined amount buttons on Payment Page (requires open amount)</span>
        <span class="accordion-arrow">▼</span>
      </div>
      <div class="accordion-body">
        <div class="grid grid-3">
          <div class="field">
            <label>suggestedAmount1</label>
            <input type="number" id="sugAmt1" placeholder="e.g. 10" step="0.01">
          </div>
          <div class="field">
            <label>suggestedAmount2</label>
            <input type="number" id="sugAmt2" placeholder="e.g. 25" step="0.01">
          </div>
          <div class="field">
            <label>suggestedAmount3</label>
            <input type="number" id="sugAmt3" placeholder="e.g. 50" step="0.01">
          </div>
        </div>
      </div>
    </div>

    <!-- Customer Details -->
    <div class="accordion" id="acc-customer">
      <div class="accordion-header" onclick="toggleAcc('acc-customer')">
        <span class="acc-title">Customer Details</span>
        <span class="acc-desc">email + country mandatory; phone required for 3DS if no email</span>
        <span class="accordion-arrow">▼</span>
      </div>
      <div class="accordion-body">
        <div class="grid grid-2">
          <div class="field">
            <label>email <span class="req">✱ mandatory</span></label>
            <input type="text" id="cEmail" placeholder="[email protected]">
          </div>
          <div class="field">
            <label>country <span class="req">✱ mandatory</span></label>
            <select id="cCountry">
              <option value="">— select country —</option>
              <option value="AF">Afghanistan (AF)</option><option value="AL">Albania (AL)</option><option value="DZ">Algeria (DZ)</option><option value="AD">Andorra (AD)</option><option value="AO">Angola (AO)</option><option value="AG">Antigua and Barbuda (AG)</option><option value="AR">Argentina (AR)</option><option value="AM">Armenia (AM)</option><option value="AU">Australia (AU)</option><option value="AT">Austria (AT)</option><option value="AZ">Azerbaijan (AZ)</option><option value="BS">Bahamas (BS)</option><option value="BH">Bahrain (BH)</option><option value="BD">Bangladesh (BD)</option><option value="BB">Barbados (BB)</option><option value="BY">Belarus (BY)</option><option value="BE">Belgium (BE)</option><option value="BZ">Belize (BZ)</option><option value="BJ">Benin (BJ)</option><option value="BT">Bhutan (BT)</option><option value="BO">Bolivia (BO)</option><option value="BA">Bosnia and Herzegovina (BA)</option><option value="BW">Botswana (BW)</option><option value="BR">Brazil (BR)</option><option value="BN">Brunei (BN)</option><option value="BG">Bulgaria (BG)</option><option value="BF">Burkina Faso (BF)</option><option value="BI">Burundi (BI)</option><option value="KH">Cambodia (KH)</option><option value="CM">Cameroon (CM)</option><option value="CA">Canada (CA)</option><option value="CV">Cape Verde (CV)</option><option value="CF">Central African Republic (CF)</option><option value="TD">Chad (TD)</option><option value="CL">Chile (CL)</option><option value="CN">China (CN)</option><option value="CO">Colombia (CO)</option><option value="KM">Comoros (KM)</option><option value="CG">Congo (CG)</option><option value="CR">Costa Rica (CR)</option><option value="HR">Croatia (HR)</option><option value="CU">Cuba (CU)</option><option value="CY">Cyprus (CY)</option><option value="CZ">Czech Republic (CZ)</option><option value="DK">Denmark (DK)</option><option value="DJ">Djibouti (DJ)</option><option value="DM">Dominica (DM)</option><option value="DO">Dominican Republic (DO)</option><option value="EC">Ecuador (EC)</option><option value="EG">Egypt (EG)</option><option value="SV">El Salvador (SV)</option><option value="GQ">Equatorial Guinea (GQ)</option><option value="ER">Eritrea (ER)</option><option value="EE">Estonia (EE)</option><option value="ET">Ethiopia (ET)</option><option value="FJ">Fiji (FJ)</option><option value="FI">Finland (FI)</option><option value="FR">France (FR)</option><option value="GA">Gabon (GA)</option><option value="GM">Gambia (GM)</option><option value="GE">Georgia (GE)</option><option value="DE">Germany (DE)</option><option value="GH">Ghana (GH)</option><option value="GI">Gibraltar (GI)</option><option value="GR">Greece (GR)</option><option value="GL">Greenland (GL)</option><option value="GD">Grenada (GD)</option><option value="GT">Guatemala (GT)</option><option value="GN">Guinea (GN)</option><option value="GY">Guyana (GY)</option><option value="HT">Haiti (HT)</option><option value="HN">Honduras (HN)</option><option value="HK">Hong Kong (HK)</option><option value="HU">Hungary (HU)</option><option value="IS">Iceland (IS)</option><option value="IN">India (IN)</option><option value="ID">Indonesia (ID)</option><option value="IR">Iran (IR)</option><option value="IQ">Iraq (IQ)</option><option value="IE">Ireland (IE)</option><option value="IL">Israel (IL)</option><option value="IT">Italy (IT)</option><option value="JM">Jamaica (JM)</option><option value="JP">Japan (JP)</option><option value="JO">Jordan (JO)</option><option value="KZ">Kazakhstan (KZ)</option><option value="KE">Kenya (KE)</option><option value="KI">Kiribati (KI)</option><option value="KP">Korea, DPR (KP)</option><option value="KR">Korea, Republic (KR)</option><option value="KW">Kuwait (KW)</option><option value="KG">Kyrgyzstan (KG)</option><option value="LA">Laos (LA)</option><option value="LV">Latvia (LV)</option><option value="LB">Lebanon (LB)</option><option value="LS">Lesotho (LS)</option><option value="LR">Liberia (LR)</option><option value="LY">Libya (LY)</option><option value="LI">Liechtenstein (LI)</option><option value="LT">Lithuania (LT)</option><option value="LU">Luxembourg (LU)</option><option value="MO">Macao (MO)</option><option value="MK">Macedonia (MK)</option><option value="MG">Madagascar (MG)</option><option value="MW">Malawi (MW)</option><option value="MY">Malaysia (MY)</option><option value="MV">Maldives (MV)</option><option value="ML">Mali (ML)</option><option value="MT">Malta (MT)</option><option value="MX">Mexico (MX)</option><option value="MD">Moldova (MD)</option><option value="MC">Monaco (MC)</option><option value="MN">Mongolia (MN)</option><option value="ME">Montenegro (ME)</option><option value="MA">Morocco (MA)</option><option value="MZ">Mozambique (MZ)</option><option value="MM">Myanmar (MM)</option><option value="NA">Namibia (NA)</option><option value="NP">Nepal (NP)</option><option value="NL">Netherlands (NL)</option><option value="NZ">New Zealand (NZ)</option><option value="NI">Nicaragua (NI)</option><option value="NE">Niger (NE)</option><option value="NG">Nigeria (NG)</option><option value="NO">Norway (NO)</option><option value="OM">Oman (OM)</option><option value="PK">Pakistan (PK)</option><option value="PA">Panama (PA)</option><option value="PG">Papua New Guinea (PG)</option><option value="PY">Paraguay (PY)</option><option value="PE">Peru (PE)</option><option value="PH">Philippines (PH)</option><option value="PL">Poland (PL)</option><option value="PT">Portugal (PT)</option><option value="QA">Qatar (QA)</option><option value="RO">Romania (RO)</option><option value="RU">Russian Federation (RU)</option><option value="RW">Rwanda (RW)</option><option value="SA">Saudi Arabia (SA)</option><option value="SN">Senegal (SN)</option><option value="RS">Serbia (RS)</option><option value="SC">Seychelles (SC)</option><option value="SL">Sierra Leone (SL)</option><option value="SG">Singapore (SG)</option><option value="SK">Slovakia (SK)</option><option value="SI">Slovenia (SI)</option><option value="SO">Somalia (SO)</option><option value="ZA">South Africa (ZA)</option><option value="ES">Spain (ES)</option><option value="LK">Sri Lanka (LK)</option><option value="SD">Sudan (SD)</option><option value="SR">Suriname (SR)</option><option value="SE">Sweden (SE)</option><option value="CH">Switzerland (CH)</option><option value="SY">Syria (SY)</option><option value="TW">Taiwan (TW)</option><option value="TJ">Tajikistan (TJ)</option><option value="TZ">Tanzania (TZ)</option><option value="TH">Thailand (TH)</option><option value="TG">Togo (TG)</option><option value="TO">Tonga (TO)</option><option value="TT">Trinidad and Tobago (TT)</option><option value="TN">Tunisia (TN)</option><option value="TR">Turkey (TR)</option><option value="TM">Turkmenistan (TM)</option><option value="UG">Uganda (UG)</option><option value="UA">Ukraine (UA)</option><option value="AE">United Arab Emirates (AE)</option><option value="GB">United Kingdom (GB)</option><option value="US">United States (US)</option><option value="UY">Uruguay (UY)</option><option value="UZ">Uzbekistan (UZ)</option><option value="VU">Vanuatu (VU)</option><option value="VE">Venezuela (VE)</option><option value="VN">Viet Nam (VN)</option><option value="YE">Yemen (YE)</option><option value="ZM">Zambia (ZM)</option><option value="ZW">Zimbabwe (ZW)</option>
            </select>
          </div>
          <div class="field">
            <label>first_name <span class="hint">Char(30)</span></label>
            <input type="text" id="cFirstName" placeholder="First name">
          </div>
          <div class="field">
            <label>last_name <span class="hint">Char(40)</span></label>
            <input type="text" id="cLastName" placeholder="Last name">
          </div>
          <div class="field">
            <label>title</label>
            <input type="text" id="cTitle" placeholder="Mr., Mrs., Dr.">
          </div>
          <div class="field">
            <label>phone1 <span class="hint">Char(18) – req for 3DS if no email</span></label>
            <input type="text" id="cPhone1" placeholder="+35929001234">
          </div>
          <div class="field">
            <label>phone2 <span class="hint">Char(18)</span></label>
            <input type="text" id="cPhone2" placeholder="">
          </div>
          <div class="field">
            <label>address1 <span class="hint">Char(60)</span></label>
            <input type="text" id="cAddress1" placeholder="Street address">
          </div>
          <div class="field">
            <label>address2 <span class="hint">Char(60)</span></label>
            <input type="text" id="cAddress2" placeholder="Apt, suite, etc.">
          </div>
          <div class="field">
            <label>city <span class="hint">Char(30)</span></label>
            <input type="text" id="cCity" placeholder="City">
          </div>
          <div class="field">
            <label>state <span class="hint">subdivision code</span></label>
            <input type="text" id="cState" placeholder="e.g. CA">
          </div>
          <div class="field">
            <label>zip <span class="hint">Char(10)</span></label>
            <input type="text" id="cZip" placeholder="ZIP / postal code">
          </div>
          <div class="field">
            <label>personal_id <span class="hint">Char(13)</span></label>
            <input type="text" id="cPersonalId" placeholder="National ID number">
          </div>
          <div class="field">
            <label>dateOfBirth <span class="hint">YYYY-MM-DD</span></label>
            <input type="text" id="cDateOfBirth" placeholder="1990-01-15">
          </div>
          <div class="field">
            <label>existingCustomer</label>
            <div class="radio-group">
              <label class="radio-pill"><input type="radio" name="existCust" value=""> — not set —</label>
              <label class="radio-pill"><input type="radio" name="existCust" value="True"> True</label>
              <label class="radio-pill"><input type="radio" name="existCust" value="False"> False</label>
            </div>
          </div>
          <div class="field">
            <label>currentNumberOfTransactions</label>
            <input type="number" id="cTxCount" placeholder="Past transaction count">
          </div>
          <div class="field">
            <label>customerSince <span class="hint">date customer joined</span></label>
            <input type="text" id="cSince" placeholder="e.g. 2020-01-01">
          </div>
        </div>
      </div>
    </div>

    <!-- Navigation / URLs -->
    <div class="accordion" id="acc-urls">
      <div class="accordion-header" onclick="toggleAcc('acc-urls')">
        <span class="acc-title">Navigation & URL Parameters</span>
        <span class="acc-desc">success_url, error_url, pending_url, notify_url (DMN), back_url</span>
        <span class="accordion-arrow">▼</span>
      </div>
      <div class="accordion-body">
        <div class="grid">
          <div class="field">
            <label>success_url</label>
            <input type="text" id="successUrl" placeholder="https://yoursite.com/success">
          </div>
          <div class="field">
            <label>error_url</label>
            <input type="text" id="errorUrl" placeholder="https://yoursite.com/fail">
          </div>
          <div class="field">
            <label>pending_url</label>
            <input type="text" id="pendingUrl" placeholder="https://yoursite.com/pending">
          </div>
          <div class="field">
            <label>notify_url <span class="hint">DMN</span></label>
            <input type="text" id="notifyUrl" placeholder="https://yoursite.com/dmn">
          </div>
          <div class="field">
            <label>back_url</label>
            <input type="text" id="backUrl" placeholder="https://yoursite.com/back">
          </div>
          <div class="field">
            <label>appUrl <span class="hint">APM redirect back URL</span></label>
            <input type="text" id="appUrl" placeholder="https://yourapp.com/return">
          </div>
        </div>
        <div class="grid grid-2" style="margin-top:12px;">
          <div class="field">
            <label>skip_review_tab</label>
            <div class="radio-group">
              <label class="radio-pill"><input type="radio" name="skipReview" value=""> — not set —</label>
              <label class="radio-pill"><input type="radio" name="skipReview" value="true"> true</label>
              <label class="radio-pill"><input type="radio" name="skipReview" value="false"> false</label>
            </div>
          </div>
          <div class="field">
            <label>skip_billing_tab</label>
            <div class="radio-group">
              <label class="radio-pill"><input type="radio" name="skipBilling" value=""> — not set —</label>
              <label class="radio-pill"><input type="radio" name="skipBilling" value="True"> True</label>
              <label class="radio-pill"><input type="radio" name="skipBilling" value="False"> False</label>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Shipping Details -->
    <div class="accordion" id="acc-shipping">
      <div class="accordion-header" onclick="toggleAcc('acc-shipping')">
        <span class="acc-title">Shipping Details</span>
        <span class="acc-desc">Required when physical goods shipping is involved</span>
        <span class="accordion-arrow">▼</span>
      </div>
      <div class="accordion-body">
        <div class="grid grid-2">
          <div class="field">
            <label>shippingFirstName <span class="hint">Char(30)</span></label>
            <input type="text" id="shipFirstName" placeholder="Recipient first name">
          </div>
          <div class="field">
            <label>shippingLastName <span class="hint">Char(40)</span></label>
            <input type="text" id="shipLastName" placeholder="Recipient last name">
          </div>
          <div class="field">
            <label>shippingAddress <span class="hint">Char(60)</span></label>
            <input type="text" id="shipAddress" placeholder="Shipping street address">
          </div>
          <div class="field">
            <label>shippingCity <span class="hint">Char(30)</span></label>
            <input type="text" id="shipCity" placeholder="Shipping city">
          </div>
          <div class="field">
            <label>shippingCountry <span class="hint">ISO code</span></label>
            <input type="text" id="shipCountry" placeholder="e.g. GB">
          </div>
          <div class="field">
            <label>shippingZip <span class="hint">Char(10)</span></label>
            <input type="text" id="shipZip" placeholder="Shipping ZIP">
          </div>
          <div class="field">
            <label>shippingState <span class="hint">2-letter</span></label>
            <input type="text" id="shipState" placeholder="e.g. CA">
          </div>
          <div class="field">
            <label>shippingCompanyName <span class="hint">Char(255)</span></label>
            <input type="text" id="shipCompany" placeholder="Company name (if applicable)">
          </div>
          <div class="field">
            <label>shippingStreetNumber <span class="hint">Char(20)</span></label>
            <input type="text" id="shipStreetNum" placeholder="House/building number">
          </div>
          <div class="field">
            <label>AddressType</label>
            <select id="shipAddressType">
              <option value="">— not set —</option>
              <option value="Standard">Standard</option>
              <option value="ShipTo">ShipTo</option>
              <option value="PickUpPoint">PickUpPoint</option>
              <option value="InStore">InStore</option>
            </select>
          </div>
          <div class="field">
            <label>shipIndicator <span class="hint">3DS ship method</span></label>
            <select id="shipIndicator">
              <option value="">— not set —</option>
              <option value="01">01 – Ship to billing address</option>
              <option value="02">02 – Ship to verified address on file</option>
              <option value="03">03 – Ship to different address</option>
              <option value="04">04 – Ship to store / pickup</option>
              <option value="05">05 – Digital goods</option>
              <option value="06">06 – Travel / Event tickets</option>
              <option value="07">07 – Other</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- 3DS Parameters -->
    <div class="accordion" id="acc-3ds">
      <div class="accordion-header" onclick="toggleAcc('acc-3ds')">
        <span class="acc-title">3DS / SCA Parameters</span>
        <span class="acc-desc">Optional but highly recommended — more params = higher frictionless rate</span>
        <span class="accordion-arrow">▼</span>
      </div>
      <div class="accordion-body">
        <div class="grid grid-2">
          <div class="field">
            <label>deliveryEmail <span class="hint">for digital goods</span></label>
            <input type="text" id="deliveryEmail" placeholder="Delivery email">
          </div>
          <div class="field">
            <label>deliveryTimeFrame</label>
            <select id="deliveryTimeFrame">
              <option value="">— not set —</option>
              <option value="01">01 – Electronic delivery</option>
              <option value="02">02 – Same day shipping</option>
              <option value="03">03 – Overnight shipping</option>
              <option value="04">04 – Two-day or more</option>
            </select>
          </div>
          <div class="field">
            <label>preOrderDate <span class="hint">YYYYMMDD</span></label>
            <input type="text" id="preOrderDate" placeholder="20260101">
          </div>
          <div class="field">
            <label>preOrderInd</label>
            <select id="preOrderInd">
              <option value="">— not set —</option>
              <option value="01">01 – Merchandise available</option>
              <option value="02">02 – Future availability</option>
            </select>
          </div>
          <div class="field">
            <label>reorderItemsInd</label>
            <select id="reorderItemsInd">
              <option value="">— not set —</option>
              <option value="01">01 – First time ordered</option>
              <option value="02">02 – Reordered</option>
            </select>
          </div>
          <div class="field">
            <label>threeDSChallengeWindowSize</label>
            <select id="threeDSChallengeWindowSize">
              <option value="">— not set (default 03) —</option>
              <option value="01">01 – 250x400</option>
              <option value="02">02 – 390x400</option>
              <option value="03">03 – 500x600</option>
              <option value="04">04 – 600x400</option>
              <option value="05">05 – Full screen</option>
            </select>
          </div>
          <div class="field">
            <label>accountAge</label>
            <select id="accountAge">
              <option value="">— not set —</option>
              <option value="01">01 – No account (guest)</option>
              <option value="02">02 – Created this transaction</option>
              <option value="03">03 – Less than 30 days</option>
              <option value="04">04 – 30–60 days</option>
              <option value="05">05 – More than 60 days</option>
            </select>
          </div>
          <div class="field">
            <label>accountLastChangeDate <span class="hint">YYYYMMDD</span></label>
            <input type="text" id="accountLastChangeDate" placeholder="20260101">
          </div>
          <div class="field">
            <label>accountLastChangeInd</label>
            <select id="accountLastChangeInd">
              <option value="">— not set —</option>
              <option value="01">01 – Changed this transaction</option>
              <option value="02">02 – Less than 30 days</option>
              <option value="03">03 – 30–60 days</option>
              <option value="04">04 – More than 60 days</option>
            </select>
          </div>
          <div class="field">
            <label>accountRegistrationDate <span class="hint">YYYYMMDD</span></label>
            <input type="text" id="accountRegistrationDate" placeholder="20260101">
          </div>
          <div class="field">
            <label>accountPasswordChangeDate <span class="hint">YYYYMMDD</span></label>
            <input type="text" id="accountPasswordChangeDate" placeholder="20260101">
          </div>
          <div class="field">
            <label>accountResetInd</label>
            <select id="accountResetInd">
              <option value="">— not set —</option>
              <option value="01">01 – No change</option>
              <option value="02">02 – Changed this transaction</option>
              <option value="03">03 – Less than 30 days</option>
              <option value="04">04 – 30–60 days</option>
              <option value="05">05 – More than 60 days</option>
            </select>
          </div>
          <div class="field">
            <label>accountPurchasesCount6M</label>
            <input type="number" id="accountPurchasesCount6M" placeholder="Number of purchases (6 months)">
          </div>
          <div class="field">
            <label>accountAddCardAttempts24H</label>
            <input type="number" id="accountAddCardAttempts24H" placeholder="Add card attempts (24h)">
          </div>
          <div class="field">
            <label>accountTransactionsCount24H</label>
            <input type="number" id="accountTransactionsCount24H" placeholder="Transactions (24h)">
          </div>
          <div class="field">
            <label>accountTransactionsCount1Y</label>
            <input type="number" id="accountTransactionsCount1Y" placeholder="Transactions (1 year)">
          </div>
          <div class="field">
            <label>accountAddressFirstUseDate <span class="hint">YYYYMMDD</span></label>
            <input type="text" id="accountAddressFirstUseDate" placeholder="20260101">
          </div>
          <div class="field">
            <label>accountAddressFirstUseInd</label>
            <select id="accountAddressFirstUseInd">
              <option value="">— not set —</option>
              <option value="01">01 – This transaction</option>
              <option value="02">02 – Less than 30 days</option>
              <option value="03">03 – 30–60 days</option>
              <option value="04">04 – More than 60 days</option>
            </select>
          </div>
          <div class="field">
            <label>accountNameInd</label>
            <select id="accountNameInd">
              <option value="">— not set —</option>
              <option value="01">01 – Account name = shipping name</option>
              <option value="02">02 – Account name ≠ shipping name</option>
            </select>
          </div>
          <div class="field">
            <label>accountSuspiciousActivityInd</label>
            <select id="accountSuspiciousActivityInd">
              <option value="">— not set —</option>
              <option value="01">01 – No suspicious activity</option>
              <option value="02">02 – Suspicious activity observed</option>
            </select>
          </div>
          <div class="field">
            <label>giftCardAmount</label>
            <input type="text" id="giftCardAmount" placeholder="Gift card amount (major units)">
          </div>
          <div class="field">
            <label>giftCardCount <span class="hint">max 2 digits</span></label>
            <input type="number" id="giftCardCount" placeholder="Number of gift cards">
          </div>
          <div class="field">
            <label>giftCardCurrency <span class="hint">ISO 4217</span></label>
            <input type="text" id="giftCardCurrency" placeholder="e.g. EUR" maxlength="3">
          </div>
        </div>
      </div>
    </div>

    <!-- Merchant Custom Fields -->
    <div class="accordion" id="acc-custom-fields">
      <div class="accordion-header" onclick="toggleAcc('acc-custom-fields')">
        <span class="acc-title">Merchant Custom Fields</span>
        <span class="acc-desc">customField1–15, descriptor name/phone, customSiteName</span>
        <span class="accordion-arrow">▼</span>
      </div>
      <div class="accordion-body">
        <div class="grid grid-2" style="margin-bottom:14px;">
          <div class="field">
            <label>descriptorMerchantName <span class="hint">Char(25)</span></label>
            <input type="text" id="descName" placeholder="Dynamic merchant name">
          </div>
          <div class="field">
            <label>descriptorMerchantPhone <span class="hint">Char(13)</span></label>
            <input type="text" id="descPhone" placeholder="+35929001234">
          </div>
          <div class="field">
            <label>customSiteName <span class="hint">Char(50)</span></label>
            <input type="text" id="customSiteName" placeholder="Override MerchantSite name">
          </div>
        </div>
        <div class="label-sm">customField1 – customField15 (Char 64 each)</div>
        <div id="customFieldsContainer"></div>
        <button class="btn btn-secondary btn-sm" onclick="addCustomField()">+ Add Custom Field</button>
      </div>
    </div>

    <!-- Credit Card Regulation -->
    <div class="accordion" id="acc-ccreq">
      <div class="accordion-header" onclick="toggleAcc('acc-ccreq')">
        <span class="acc-title">Credit Card Regulation</span>
        <span class="acc-desc">Annual deposit limits — contact Nuvei Integration before using</span>
        <span class="accordion-arrow">▼</span>
      </div>
      <div class="accordion-body">
        <div class="grid grid-2">
          <div class="field">
            <label>creditCardDepositAmount <span class="hint">deposited this calendar year</span></label>
            <input type="number" id="ccDepAmt" placeholder="0.00" step="0.01">
          </div>
          <div class="field">
            <label>MaxCreditCardDepositAmount <span class="hint">annual maximum allowed</span></label>
            <input type="number" id="ccMaxDepAmt" placeholder="0.00" step="0.01">
          </div>
          <div class="field">
            <label>withdrawableBalance <span class="hint">max withdrawable (withdraw mode)</span></label>
            <input type="number" id="withdrawableBalance" placeholder="0.00" step="0.01">
          </div>
          <div class="field">
            <label>digitalAssetType</label>
            <select id="digitalAssetType">
              <option value="">— not set —</option>
              <option value="1">1 – CBDC / Tokenized Deposits</option>
              <option value="2">2 – Stable Coin (Fiat-backed)</option>
              <option value="3">3 – Blockchain Native Token/Coin</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Timestamp -->
    <div class="card">
      <div class="card-title">Timestamp & Checksum</div>
      <div class="grid grid-2">
        <div class="field ts-row">
          <div class="field" style="flex:1;">
            <label>time_stamp <span class="req">✱</span> <span class="hint">YYYY-MM-DD.HH:MM:SS (GMT)</span></label>
            <input type="text" id="depTimestamp" placeholder="2026-06-28.12:00:00">
          </div>
          <button class="btn btn-secondary" onclick="setCurrentTimestamp('dep')" style="margin-bottom:0;height:36px;">Now</button>
        </div>
      </div>
    </div>

    <!-- Free-form additional params -->
    <div class="accordion" id="acc-freeform">
      <div class="accordion-header" onclick="toggleAcc('acc-freeform')">
        <span class="acc-title">Additional Free-Form Parameters</span>
        <span class="acc-desc">Any key=value pairs not covered above (included in checksum if added)</span>
        <span class="accordion-arrow">▼</span>
      </div>
      <div class="accordion-body">
        <div id="freeFormContainer"></div>
        <button class="btn btn-secondary btn-sm" onclick="addFreeFormParam('freeFormContainer')">+ Add Parameter</button>
      </div>
    </div>

    <!-- Calculate -->
    <div class="card">
      <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <button class="btn btn-primary" onclick="calculateDeposit()">⚡ Calculate Checksum</button>
        <button class="btn btn-secondary" onclick="clearDeposit()">Clear All</button>
      </div>
    </div>

    <!-- Result -->
    <div id="depositResult" style="display:none;" class="result-section">
      <div class="result-card">
        <div class="result-card-header">
          <span>Result</span>
          <button class="btn btn-secondary btn-sm" onclick="copyResult('depChecksum')">Copy checksum</button>
        </div>
        <div class="result-card-body">
          <div class="label-sm">Pre-hash string</div>
          <div class="code-block" id="depPreHash"></div>
          <div class="label-sm">Checksum</div>
          <div class="code-block" id="depChecksum"></div>
          <div class="label-sm">Full URL</div>
          <div class="code-block url-block" id="depUrl"></div>
          <div class="action-bar" style="margin-top:14px;">
            <button class="btn btn-success" onclick="openUrl('depUrl')">Open in New Tab</button>
            <button class="btn btn-secondary" onclick="openInIframe('depUrl')">Open in iFrame</button>
            <button class="btn btn-secondary" onclick="copyResult('depUrl')">Copy URL</button>
          </div>
          <div id="iframeContainer">
            <div style="display:flex; gap:8px; align-items:center; margin-top:10px; margin-bottom:6px;">
              <input type="number" id="iframeW" placeholder="Width px" value="800" style="width:100px;">
              <span style="color:var(--text-muted);">×</span>
              <input type="number" id="iframeH" placeholder="Height px" value="600" style="width:100px;">
              <button class="btn btn-secondary btn-sm" onclick="resizeIframe()">Resize</button>
              <button class="btn btn-danger btn-sm" onclick="closeIframe()">Close</button>
            </div>
            <iframe id="pppIframe" src="" width="800" height="600"></iframe>
          </div>
        </div>
      </div>
    </div>

  </div><!-- /panel-deposit -->


  <!-- ══════════════════════════════════════════════
       WITHDRAWAL PANEL
  ══════════════════════════════════════════════ -->
  <div id="panel-withdrawal" class="panel">

    <!-- Withdrawal mandatory -->
    <div class="card">
      <div class="card-title">Withdrawal Mandatory Parameters <span class="badge badge-req">Required</span></div>
      <div class="grid grid-2">
        <div class="field">
          <label>user_token <span class="req">✱</span></label>
          <div class="radio-group">
            <label class="radio-pill"><input type="radio" name="wdUserToken" value="auto" checked> auto</label>
            <label class="radio-pill"><input type="radio" name="wdUserToken" value="register"> register</label>
          </div>
        </div>
        <div class="field">
          <label>user_token_id <span class="req">✱</span> <span class="hint">Char(255)</span></label>
          <input type="text" id="wdUserTokenId" placeholder="Unique user ID">
        </div>
        <div class="field">
          <label>wd_amount <span class="req">✱</span></label>
          <input type="number" id="wdAmount" placeholder="Default withdrawal amount" step="0.01">
        </div>
        <div class="field">
          <label>wd_currency <span class="req">✱</span> <span class="hint">ISO 4217</span></label>
          <input type="text" id="wdCurrency" placeholder="EUR" maxlength="3" style="text-transform:uppercase;">
        </div>
        <div class="field">
          <label>version <span class="req">✱</span></label>
          <div class="radio-group">
            <label class="radio-pill"><input type="radio" name="wdVersion" value="4.0.0" checked> 4.0.0</label>
          </div>
        </div>
      </div>
    </div>

    <!-- Open amount -->
    <div class="card">
      <div class="card-title">Amount Settings <span class="badge badge-opt">Optional</span></div>
      <div class="grid grid-2">
        <div class="field">
          <label>wd_open_amount <span class="hint">default: True</span></label>
          <div class="radio-group">
            <label class="radio-pill"><input type="radio" name="wdOpenAmt" value=""> — not set (True) —</label>
            <label class="radio-pill"><input type="radio" name="wdOpenAmt" value="True"> True</label>
            <label class="radio-pill"><input type="radio" name="wdOpenAmt" value="False"> False (fixed)</label>
          </div>
        </div>
        <div class="field">
          <label>wd_min_amount <span class="hint">required if open amount</span></label>
          <input type="number" id="wdMinAmount" placeholder="Minimum withdrawal" step="0.01">
        </div>
        <div class="field">
          <label>wd_max_amount <span class="hint">required if open amount</span></label>
          <input type="number" id="wdMaxAmount" placeholder="Maximum withdrawal" step="0.01">
        </div>
      </div>
    </div>

    <!-- Withdrawal optional -->
    <div class="accordion" id="acc-wd-optional">
      <div class="accordion-header" onclick="toggleAcc('acc-wd-optional')">
        <span class="acc-title">Optional Parameters</span>
        <span class="acc-desc">locale, country, customSiteName, layout, URLs, etc.</span>
        <span class="accordion-arrow">▼</span>
      </div>
      <div class="accordion-body">
        <div class="grid grid-2">
          <div class="field">
            <label>merchantLocale</label>
            <select id="wdLocale">
              <option value="">— not set —</option>
              <option value="en_US">English (US) – en_US</option>
              <option value="en_UK">English (UK) – en_UK</option>
              <option value="bg_BG">Bulgarian – bg_BG</option>
              <option value="de_DE">German – de_DE</option>
              <option value="fr_FR">French – fr_FR</option>
              <option value="es_ES">Spanish – es_ES</option>
              <option value="it_IT">Italian – it_IT</option>
              <option value="ru_RU">Russian – ru_RU</option>
            </select>
          </div>
          <div class="field">
            <label>country <span class="hint">2-letter ISO</span></label>
            <input type="text" id="wdCountry" placeholder="e.g. BG">
          </div>
          <div class="field">
            <label>userId <span class="hint">external user ID for tracking</span></label>
            <input type="text" id="wdUserId" placeholder="Your external user ID">
          </div>
          <div class="field">
            <label>merchant_unique_id</label>
            <input type="text" id="wdMerchantUniqueId" placeholder="Merchant's unique ID for this WD">
          </div>
          <div class="field">
            <label>customSiteName <span class="hint">Char(50)</span></label>
            <input type="text" id="wdCustomSiteName" placeholder="Override site name">
          </div>
          <div class="field">
            <label>layout</label>
            <select id="wdLayout">
              <option value="">— not set —</option>
              <option value="0">0 – Gallery + Amount (no pending section)</option>
              <option value="1">1 – Pending withdrawals only</option>
              <option value="2">2 – Full with pending open</option>
              <option value="3">3 – Full with pending collapsed</option>
            </select>
          </div>
          <div class="field">
            <label>showCancelButton</label>
            <div class="radio-group">
              <label class="radio-pill"><input type="radio" name="wdShowCancel" value=""> — not set —</label>
              <label class="radio-pill"><input type="radio" name="wdShowCancel" value="1"> 1 (visible)</label>
              <label class="radio-pill"><input type="radio" name="wdShowCancel" value="0"> 0 (hidden)</label>
            </div>
          </div>
          <div class="field">
            <label>iban <span class="hint">P24 only</span></label>
            <input type="text" id="wdIban" placeholder="Customer IBAN">
          </div>
          <div class="field">
            <label>digitalAssetType</label>
            <select id="wdDigitalAssetType">
              <option value="">— not set —</option>
              <option value="1">1 – CBDC / Tokenized Deposits</option>
              <option value="2">2 – Stable Coin</option>
              <option value="3">3 – Blockchain Native Token/Coin</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- WD URLs -->
    <div class="accordion" id="acc-wd-urls">
      <div class="accordion-header" onclick="toggleAcc('acc-wd-urls')">
        <span class="acc-title">Redirect URLs</span>
        <span class="acc-desc">successUrl, failUrl, backUrl</span>
        <span class="accordion-arrow">▼</span>
      </div>
      <div class="accordion-body">
        <div class="grid">
          <div class="field">
            <label>successUrl</label>
            <input type="text" id="wdSuccessUrl" placeholder="https://yoursite.com/wd-success">
          </div>
          <div class="field">
            <label>failUrl</label>
            <input type="text" id="wdFailUrl" placeholder="https://yoursite.com/wd-fail">
          </div>
          <div class="field">
            <label>backUrl</label>
            <input type="text" id="wdBackUrl" placeholder="https://yoursite.com/back">
          </div>
        </div>
      </div>
    </div>

    <!-- WD Custom Fields -->
    <div class="accordion" id="acc-wd-cf">
      <div class="accordion-header" onclick="toggleAcc('acc-wd-cf')">
        <span class="acc-title">Custom Fields (customField1–15)</span>
        <span class="acc-desc">Custom data fields for the withdrawal request</span>
        <span class="accordion-arrow">▼</span>
      </div>
      <div class="accordion-body">
        <div id="wdCustomFieldsContainer"></div>
        <button class="btn btn-secondary btn-sm" onclick="addWdCustomField()">+ Add Custom Field</button>
      </div>
    </div>

    <!-- WD Timestamp -->
    <div class="card">
      <div class="card-title">Timestamp
        <span style="font-size:11px; color:var(--warning); font-weight:400; margin-left:8px;">⚠ Format: YYYYMMDDHHMMSS (no separators) — different from Deposit!</span>
      </div>
      <div class="ts-row">
        <div class="field" style="flex:1;">
          <label>timeStamp <span class="req">✱</span> <span class="hint">YYYYMMDDHHMMSS (GMT)</span></label>
          <input type="text" id="wdTimestamp" placeholder="20260628120000">
        </div>
        <button class="btn btn-secondary" onclick="setCurrentTimestamp('wd')" style="height:36px;">Now</button>
      </div>
      <div style="margin-top:10px; padding: 8px 12px; background:rgba(245,158,11,.08); border:1px solid rgba(245,158,11,.2); border-radius:6px; font-size:12px; color:var(--warning);">
        <strong>Checksum note:</strong> For Withdrawal redirect, the secret key is placed at the <strong>FRONT</strong> of the concatenated string (not at the end like Deposit). The tool handles this automatically.
      </div>
    </div>

    <!-- WD Free-form -->
    <div class="accordion" id="acc-wd-freeform">
      <div class="accordion-header" onclick="toggleAcc('acc-wd-freeform')">
        <span class="acc-title">Additional Free-Form Parameters</span>
        <span class="acc-desc">Any extra key=value pairs included in the request</span>
        <span class="accordion-arrow">▼</span>
      </div>
      <div class="accordion-body">
        <div id="wdFreeFormContainer"></div>
        <button class="btn btn-secondary btn-sm" onclick="addFreeFormParam('wdFreeFormContainer')">+ Add Parameter</button>
      </div>
    </div>

    <!-- WD Calculate -->
    <div class="card">
      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <button class="btn btn-primary" onclick="calculateWithdrawal()">⚡ Calculate Checksum</button>
        <button class="btn btn-secondary" onclick="clearWithdrawal()">Clear All</button>
      </div>
    </div>

    <!-- WD Result -->
    <div id="withdrawalResult" style="display:none;" class="result-section">
      <div class="result-card">
        <div class="result-card-header">
          <span>Withdrawal Result</span>
          <button class="btn btn-secondary btn-sm" onclick="copyResult('wdChecksum')">Copy checksum</button>
        </div>
        <div class="result-card-body">
          <div class="label-sm">Pre-hash string (secret key at FRONT)</div>
          <div class="code-block" id="wdPreHash"></div>
          <div class="label-sm">Checksum</div>
          <div class="code-block" id="wdChecksum"></div>
          <div class="label-sm">Full URL</div>
          <div class="code-block url-block" id="wdUrl"></div>
          <div class="action-bar" style="margin-top:14px;">
            <button class="btn btn-success" onclick="openUrl('wdUrl')">Open in New Tab</button>
            <button class="btn btn-secondary" onclick="copyResult('wdUrl')">Copy URL</button>
          </div>
        </div>
      </div>
    </div>

  </div><!-- /panel-withdrawal -->

</div><!-- /container -->


</body>
</html>