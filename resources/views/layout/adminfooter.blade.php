<div class="pull-right hidden-xs">
    <b>Version 2.1.8</b>  
    </div>
<strong>Copyright &copy; {{date('Y')}} <a href="https://thinkbitsecurity.co.uk/" target="_blank">BluescreenIT LTD.</a></strong> All rights reserved.
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"  type="text/javascript"></script>


<style type="text/css">
body {
  user-select: none; /* Disable text selection */
  -webkit-user-select: none; /* For WebKit browsers */
  -moz-user-select: none; /* For Firefox */
  -ms-user-select: none; /* For IE/Edge */
}
/* Allow text selection for specific areas */
.copyable {
  user-select: text;
  -webkit-user-select: text; /* For WebKit browsers */
  -moz-user-select: text; /* For Firefox */
  -ms-user-select: text; /* For IE/Edge */
}

.cookie-box {
    background: #fff;
    border-radius: 2px;
    box-shadow: 0 17px 17px rgba(0,0,0,.15), 0 27px 55px rgba(0,0,0,.3);
    font: 14px/20px Roboto,sans-serif;
    margin: 24px;
    max-height: calc(100% - 48px);
    max-width: calc(100% - 48px);
    overflow: auto;
    padding: 8px;
    position: fixed;
    z-index: 10012;
    right: 60px;
    bottom: 30px;
    display: none;
}

.cookie-box-contents {
    color: #757575;
    padding: 16px;
}

.cookie-box-buttons {
    text-align: right;
}

.cookie-button {
    color: #039be5;
    padding: 8px;
    margin: 0 8px;
    border: 0;
    border-radius: 2px;
    display: inline-block;
    font: 700 16px Roboto,sans-serif;
    min-width: 56px;
    outline: 0;
    overflow: hidden;
    text-align: center;
    text-decoration: none;
    text-transform: uppercase;
    transition: background-color .2s;
    vertical-align: middle;
    white-space: nowrap;
}

.cookie-button:hover {
    background-color: #e1f3fc;
    text-decoration: none;
}

.forceBgClassified {
    background-color: #2c3c64 !important;
    border-color: #2c3c64 !important;
    color: white !important;
    transition: 0.3s;
  }
.forceBgClassified:hover {
  background-color: #08457e !important;
  color: white;
}

</style>
<div class="cookie-box" id="cookie-warning">
        <div class="cookie-box-contents" id="gdpr-message">This site uses cookies to remember your preferences and optimise your experience.</div>
        <div class="cookie-box-buttons"><a href="/privacypolicy.pdf" class="cookie-button">Privacy Policy</a><a href="#" class="cookie-button" id="cookie-close">Close</a></div>
    </div>
    <script type="text/javascript">
$(function () {
    var cookieName = 'gdprAccepted'; // Name of the sessionStorage key

    // Check if the cookie warning has been accepted in this session
    if (!sessionStorage.getItem(cookieName)) {
        $('#cookie-warning').show();
    }

    $('#cookie-close').on('click', function (e) {
        e.preventDefault(); // Prevent default link behavior

        // Store a flag in sessionStorage to remember that the user has dismissed the cookie box
        sessionStorage.setItem(cookieName, true);

        // Optionally, send a request to the server if necessary
        $.ajax({
            url: "<?php echo env('APP_URL'); ?>" + "setGdprCookie", 
            method: "POST",
            data: {"_token": "{{ csrf_token() }}"},
            success: function(result) {
                $('#cookie-warning').fadeOut('slow');
            },
            error: function(result) {
                $('#cookie-warning').fadeOut('slow');
            },
        });
    });
});
</script>

<!--security-->
<script>
  document.addEventListener('contextmenu', (e) => {
    e.preventDefault();
  });

  // Disable copying globally
  document.addEventListener('copy', (e) => {
    const target = e.target;

    // Allow copying only for elements with the class 'copyable'
    if (!target.classList.contains('copyable')) {
        e.preventDefault();
        alert('Copying is disabled to protect sensitive information.');
    }
  });

  /*document.addEventListener('copy', (e) => {
    e.preventDefault();
    alert('Copying is disabled to protect sensitive information.');
  });*/

  const printBlocker = document.createElement('div');
  printBlocker.style = `
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: white;
    z-index: 9999;
  `;

  window.onbeforeprint = () => {
    document.body.appendChild(printBlocker);
  };

  window.onafterprint = () => {
    document.body.removeChild(printBlocker);
  };

  document.addEventListener('keydown', (e) => {
    if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && e.key === 'I')) {
        e.preventDefault();
    }
  });


</script>

