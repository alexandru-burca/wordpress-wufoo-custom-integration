# Worpdress & Wufoo - API integration

Wufoo API - https://wufoo.github.io/docs/

The only way to integrate **Wufoo** with **Wordpress** is through iframe. This brings a lot of impiediments. Well, this plugin comes to fix this by integrating any HTML forms with Wufoo.

**There are a few rules to follow. To use the plugin please follow the following procedure:**
1. Install the plugin
1. Add the **reCaptcha Site** and **Secret Key** under the "Settings" sub-menu
1. Fill the **"Wufoo API Key"** with your wufoo key:
	1. Log in into the Wufoo account
	1. From the Form Manager, select API Information from the More dropdown on any form.
	1. On that page there is a 16 digit code, which is the unique API key.
1. Download and copy the HTML form
1. Click on "Add New" under "Wufoo Forms" menu
1. Paste the HTML form
1. Insert the reCaptcha shortcode (https://codex.wordpress.org/Shortcode) insite the form tag 
	```
	<form>
	...
	[wure-recaptcha]
	..
	</form>
	```
1. Paste the form id
1. Save the form
1. Copy and paste the generated shortcode where do you want to appear the form

**Suported field types**
- Single line text
- Paragraph text
- Multiple choise
- Number
- Checkbox
- Dropdown
- Name
- Address
- Email Address
- Phone
- File Upload

**Also supports**
- Redirect *Thank You* page
- Logs

**Example HTML form**
```
<form method="post">
	<header id="header" class="info">
		<h2 class="0">Test</h2>
		<div class="0"></div>
	</header>
	<ul>
		<li id="foli1" data-wufoo-field data-field-type="text" class="notranslate      ">
			<label class="desc" id="title1" for="Field1">
				First Name
			</label>
			<div>
				<input id="Field1" name="Field1" type="text" class="field text medium" value="" maxlength="255" tabindex="0" onkeyup=""       placeholder="" />
			</div>
		</li>
		<li id="foli8" class="notranslate      ">
			<label class="desc" id="title8" for="Field8">
				Email
			</label>
			<div>
				<input id="Field8" name="Field8" type="email" spellcheck="false" class="field text medium" value="" maxlength="255" tabindex="0"       placeholder="" />
			</div>
		</li>
		<!--reCaptcha shortcode-->
		[wure-recaptcha]
		<li class="buttons ">
			<div>
				<input id="saveForm" name="saveForm" class="btTxt submit" type="submit" value="Submit"/>    
			</div>
		</li>
	</ul>
</form>
```

**Security & Anti-Spam checklist**
- reCaptcha
- Honeypot
- WP nonce

**IMPORTANT:** *The HTML `<input>` name should match the same attribute as the wufoo form*

**Changelog**
 - 0.0.2 - Register logs into DB instead a `.txt` file
 - 0.0.1 - First Version