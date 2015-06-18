What I use for contact forms and to add emails to contact lists. The actual sending is via PHPMailer.

`lists.php` decrypts my store of e-mail lists and displays them.

```
<form method="post" action="http://robertakarobin.com/mail/mail.php" target="_blank">
  <textarea name="body" placeholder="Say hello!"></textarea>
  <input type="hidden" name="emailMe" value="robin@magnetichtml.com" />
  <input type="hidden" name="render" value="html" />
  <input type="hidden" name="subject" value="Thanks for your message!" />
  <input type="text" name="emailThem" placeholder="hello@email.com" />
  <input name="emailList" id="emailList" type="checkbox" /><label for="emailList">Add me to the email list</label>
  <div class="g-recaptcha" data-theme="light" data-sitekey="6LfQiAcTAAAAAGLPo6q0xMh1S42WUp4H1fd9cJQT"></div>
  <button type="submit">Send to robin@magnetichtml.com</button>
</form>
```

```
(function loadCaptcha(){
  var script = document.createElement("SCRIPT");
  script.setAttribute("type", "text/javascript");
  script.setAttribute("src", "https://www.google.com/recaptcha/api.js");
  document.getElementsByTagName("head")[0].appendChild(script);
})();
```
