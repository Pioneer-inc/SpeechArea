<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>AT&amp;T Sample Application - Speech To Text</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="IMMN Sample Application">
    <meta name="author" content="SECRET">

    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <![endif]-->

    <!-- jquery lib -->
    <script src="js/jquery.min.js"></script>

    <!-- bootstrap lib -->	
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css"/>
    <link rel="stylesheet" type="text/css" href="css/bootstrap-responsive.css"/>
    <script src="js/bootstrap-tab.js"></script>
    <script src="js/bootstrap-transition.js"></script>
    <script src="js/bootstrap-alert.js"></script>
    <script src="js/bootstrap-modal.js"></script>
    <script src="js/bootstrap-dropdown.js"></script>
    <script src="js/bootstrap-scrollspy.js"></script>
    <script src="js/bootstrap-tab.js"></script>
    <script src="js/bootstrap-tooltip.js"></script>
    <script src="js/bootstrap-popover.js"></script>
    <script src="js/bootstrap-button.js"></script>
    <script src="js/bootstrap-collapse.js"></script>
    <script src="js/bootstrap-carousel.js"></script>
    <script src="js/bootstrap-typeahead.js"></script>

    <script src="js/audiodisplay.js"></script>
    <script src="js/recorderjs/recorder.js"></script>
    <script src="js/main.js"></script>

    <!-- custom lib -->
    <link rel="stylesheet" type="text/css" href="css/custom.css"/>

    <!-- set up bootstrap -->
    <script type="text/javascript">
      $(document).ready(function () {
        $('#myTab a').click(function (e) {
          e.preventDefault();
          $(this).tab('show');
        });
      });
    </script>
    <script>
      var recording = false;

      function setAlertText(txt) {
        document.getElementById("alertDiv").style.display = 'inline';
        document.getElementById("innerAlertDiv").innerHTML = txt;
      }

      function setTableText(txt) {
        document.getElementById("tableDiv").style.display = 'inline';
        document.getElementById("tableDiv").innerHTML = txt;
      }

      function hideTableText() {
        document.getElementById("tableDiv").style.display = 'none';
      }

      function showCanvas() {
        document.getElementById("canvasDiv").style.display = 'inline';
      }

      function hideCanvas() {
        document.getElementById("canvasDiv").style.display = 'none';
      }

      function hideAlertText() {
        document.getElementById("alertDiv").style.display = 'none';
      }

      function setProgressBarText(txt) {
        document.getElementById("progressDiv").style.display = 'inline';
        document.getElementById("progressLabel").innerHTML = txt;

      }

      function hideProgressBar() {
        document.getElementById("progressDiv").style.display = 'none';
      }

      function sendSpeechRequest() {
        /*
          if (rawAudio == null) {
            setAlertText('You must first record the file!');
            return;
          }
          */

          var xmlhttp = new XMLHttpRequest();
          xmlhttp.onreadystatechange=function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
              hideProgressBar();
              setTableText(xmlhttp.responseText);
            }
          };
	  setProgressBarText('Sending request...');
          xmlhttp.open("GET", "request.php", true);
          xmlhttp.send();
          hideTableText();
      }

      function clickRecordButton(v) {
        hideTableText();
        recording = !recording;
        if (recording) {
          setAlertText('RECORDING IS IN PROGRESS!');
          document.getElementById('recording').innerHTML = 'Stop Recording';
          toggleRecording(v);
          showCanvas();
        } else { /* stop recording */
          document.getElementById('recording').innerHTML = 'Record';
          document.getElementById('flabel').innerHTML = 'Audio File: <a href="tmp2channels.wav">wav file</a>';
          hideAlertText();
          hideCanvas();
	  setProgressBarText('Uploading audio to server... please wait before pressing submit.');
          toggleRecording(v);
          saveAudio();
	  hideProgressBar();
        }
      }
    </script>
  </head>
  <body>
  <!--
	<canvas id="analyser" width="768" height="200"></canvas><br>
	<canvas id="wavedisplay" width="768" height="200"></canvas><br>
  -->
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <div class="container-fluid">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <div class="media">
            <a class="pull-left" href="#">
              <img class="media-object" src="img/att.gif">
            </a>
            <div class="media-body">
              <div class="nav-collapse collapse pull-right">
                <ul class="nav">
                  <li><a href="#" target="_blank">Full Page</a></li>
                  <li><a class="abottom" data-toggle="tooltip" 
                      title="Click here to download source code for this sample application." 
                      href="#" target="_blank">Source</a><li>
                  <li><a href="#" target="_blank">Download</a></li>
                  <li><a href="#" target="_blank">Help</a></li>
                  <li><a href="#" target="_blank">API Documentation</a></li>
                </ul>
              </div> <!--/media-body-->
            </div> <!--/media-->
          </div><!--/.nav-collapse -->
        </div> <!-- end of container-fluid -->
      </div> <!-- end of navbar-inner -->
    </div> <!-- end of navbar -->

    <div class="container-fluid">
      <div class="bwrapper">
        <div class="row-fluid">
          <div class="span12">
            <h4>Speech To Text Sample Application</h4>
          </div>
        </div>
        <div id="alertDiv" class="row-fluid">
          <div id="innerAlertDiv" class="alert alert-error">
          This application currently works only on the latest versions of google-chrome (desktop version).
          </div>
        </div>
        <div class="row-fluid">
          <div class="span12">
            <div class="wrapper">
            <!--
              <form method="post" action="index.php" name="msgContentForm">
              -->
                <fieldset>
                  <div class="row-fluid">
                    <div class="span4">
                      <label>Speech Context:</label>
                      <select name="context">
                        <option value="Generic">Generic</option>
                        <option value="TV">TV</option>
                        <option value="BusinessSearch">BusinessSearch</option>
                        <option value="Websearch">Websearch</option>
                        <option value="SMS">SMS</option>
                        <option value="Voicemail">Voicemail</option>
                        <option value="QuestionAndAnswer">QuestionAndAnswer</option>
                        <option value="Gaming">Gaming</option>
                        <option value="SocialMedia">SocialMedia</option>
                      </select>
                    </div>
                    <div class="span4">
                      <label id="flabel">Audio File:</label>
                      <button id="recording" type="text" class="submit btn btn-primary" 
                          onclick="clickRecordButton(this)">Record</button>
                          <!--
                      <select name="fname">
                        <option value="BostonCeltics.wav">BostonCeltics.wav</option>
                        <option value="california.amr">california.amr</option>
                        <option value="coffee.amr">coffee.amr</option>
                        <option value="doctors.wav">doctors.wav</option>
                        <option value="nospeech.wav">nospeech.wav</option>
                      </select>
                      -->
                    </div>
                  </div><!--/row-fluid-->

                  <label class="checkbox">
                    <input name="sendChunked" type="checkbox">
                    <a class="aright" href="#" data-toggle="tooltip" 
                        title="Check this to send the request chunked.">Send Chunked</a>
                  </label>
                  <div class="row-fluid">
                    <div class="span4">
                       <label>X-Arg:</label>
                       <textarea id="x_arg" name="x_arg" readonly="readonly" rows="4" 
                           value="test=123">test=123</textarea>
                    </div><!--/.span4-->
                    <div class="span4">
                      <label>X-SpeechSubContext</label>
                      <textarea id="x_subContext" name="x_arg" readonly="readonly" rows="4" 
                          value="Chat">Chat</textarea>
                    </div><!--/.span4-->
                  </div><!--/row-fluid-->

<!--
                  <div class="row-fluid">
                    <div class="span4">
                      <button type="text" onclick="toggleRecording(this);">
                    </div>
                      <img id="record" width="128" height="128" src="img/mic128.png" onclick="toggleRecording(this);"><br>
                    </div>
                    <div class="span4">
                    
                      <button id="brecord" type="text" class="submit btn btn-primary" 
                          onclick="saveAudio(this);">Record</button>
                    </div>
                  </div>

-->
                <div id="alertDiv" class="row-fluid">
                  <div id="innerAlertDiv" class="alert alert-note">
                  After hitting 'Stop Record,' please wait a few seconds before hitting 'Submit.'
                  </div>
                </div>

                  <div class="row-fluid">
                    <div class="span12">
                      <button type="submit" class="submit btn btn-primary" id="submit" name="submit"
                        onclick="sendSpeechRequest()">Submit</button>
                    </div>
                  </div>

                  <!--
                  <div class="row-fluid">
                    <div class="span12">
                      <div class="alert alert-success">  
                        <strong>Success:</strong><br>Speech parameters listed below :)
                      </div> 
                    </div>
                  </div>
                </fieldset>
              </form>
              -->
      
              <div id="alertDiv" class="row-fluid" style="display:none">
                <div id="innerAlertDiv" class="alert alert-error">
                </div>
              </div>
              <div id="tableDiv" style="display:none">
              </div>
              <div id="canvasDiv" class="row-fluid" style="display:none">
                <canvas id="analyser" width="768" height="200"></canvas><br>
              </div>
            </div> <!--/wrapper-->
          </div> <!--./bwrapper-->
        </div> <!-- end of span offset -->
      </div> <!--/row-fluid-->
      <div id="progressDiv" class="row-fluid" style="display:none">
        <label id="progressLabel"></label>
        <div class="progress progress-striped active">
          <div class="bar" style="width: 100%;"></div>
        </div>
      </div>
      <hr>
      <footer>
        <p>
          The Application hosted on this site are working examples intended to be used for reference in creating 
          products to consume AT&amp;T Services and not meant to be used as part of your product. The data in 
          these pages is for test purposes only and intended only for use as a reference in how the services 
          perform. 
          <br><br> 
          For download of tools and documentation, please go to 
          <a href="https://devconnect-api.att.com/" target="_blank">https://devconnect-api.att.com</a>
          <br> 
          For more information contact 
          <a href="mailto:developer.support@att.com">developer.support@att.com</a>
          <br><br>
          &copy; 2013 AT&amp;T Intellectual Property. All rights reserved.
          <a href="http://developer.att.com/" target="_blank">http://developer.att.com</a>
        </p>
      </footer>
    </div> <!--/container-fluid-->

    <script>
      $('#myTab a:first').tab('show');
      $('input').tooltip({
        'selector': '',
        'placement': 'right'
      });
      $('.abottom').tooltip({
        'selector': '',
        'placement': 'bottom'
      });
      $('.aright').tooltip({
        'selector': '',
        'placement': 'right'
      });
    </script>
  </body>
</html>
