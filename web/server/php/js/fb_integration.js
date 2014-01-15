// Facebook integration JavaScript source code
function fb_prepost_check() {
    FB.getLoginStatus(
      function (resp) {
          if (resp.status == 'connected') {
              fb_do_post_preview();
          }
          else {
              FB.login(function (response) {
                  if (response.authResponse) {
                      fb_prepost_check();
                  } else {
                      // The person cancelled the login dialog
                  }
              });
          }
      }
    );
}
function fb_do_post_preview() {
    postText = $('#textArea1').val();
    if (postText.length > 0) {
        if (copyToClipboard(postText)) {
            //must already be logged in at this point
            FB.ui({
                method: 'feed',
                //link: 'http://pioneer-inc.com/speecharea',
                //name: 'link name',
                //caption: 'test caption',
                //message: 'test'
            }, function (response) {
                if (response && response.post_id) {
                    alert('Post was published.');
                } else {
                    alert('Post was not published.');
                }
            });
        }
    }
    else {
        alert('Nothing to Post!');
    }
}
