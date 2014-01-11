<?php
session_start();
require 'src/Controller/IMMNController.php';
$controller = new IMMNController();
$controller->handleRequest();
$results = $controller->getResults();
$errors = $controller->getErrors();
require 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>AT&amp;T Sample Application - In App Messaging from Mobile Number</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="IMMN Sample Application">
    <meta name="author" content="SECRET">

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

    <!-- custom lib -->
    <link rel="stylesheet" type="text/css" href="css/custom.css"/>

    <!-- IE HTML5 Compatibility -->	
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <![endif]-->

    <!-- set up bootstrap -->
    <script type="text/javascript">
      $(document).ready(function () {
        $('#myTab a').click(function (e) {
          e.preventDefault();
          $(this).tab('show');
        });
      });
    </script>
  </head>
  <body>
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
                      href="<?php echo $linkSource ?>" target="_blank">Source</a><li>
                  <li><a href="<?php echo $linkDownload ?>" target="_blank">Download</a></li>
                  <li><a href="<?php echo $linkHelp ?>" target="_blank">Help</a></li>
                  <li><a href="<?php echo $linkHelp ?>" target="_blank">API Documentation</a></li>
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
            <h4>IMMN Sample Application</h4>
          </div>
        </div>
        <div class="row-fluid">
            <ul id="myTab" class="nav nav-tabs">
              <li><a href="#sendMessages" data-toggle="tab">Send Messages</a></li>
              <li><a href="#getMessageHeaders" data-toggle="tab">Message Headers</a></li>
              <li><a href="#getMessageContent" data-toggle="tab">Message Content</a></li>
            </ul>
        </div>
        <div class="row-fluid">
          <div class="span12">
            <div class="wrapper">
              <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade in activeid" id="sendMessages">
                  <form method="post" action="index.php" name="msgContentForm">
                    <fieldset>
                      <div class="row-fluid">
                        <div class="span4">
                          <label>Address (format: 5551231234):</label>
                          <input placeholder="Address" name="Address" type="text" data-toggle="tooltip" 
                              title="Enter the address to which the message will be sent."
                              value="<?php echo htmlspecialchars(isset($_SESSION['Address']) ? $_SESSION['Address'] : ''); ?>"> 
                        </div>
                        <div class="span4">
                          <label>Message:</label>
                          <select name="message">
                            <option value="ATT IMMN sample message">ATT IMMN sample message</option>
                          </select>
                        </div>
                      </div><!--/row-fluid-->
                      <label class="checkbox">
                        <input name="groupCheckBox" type="checkbox">
                        <a class="aright" href="#" data-toggle="tooltip" 
                            title="Check this to send message as a group message.">Group</a>
                      </label>

                      <div class="row-fluid">
                        <div class="span4">
                          <label>Subject:</label>
                          <select name="subject">
                            <option value="ATT IMMN sample subject">ATT IMMN sample subject</option>
                          </select>
                        </div>
                        <div class="span4">
                          <label>Attachment:</label>
                          <select name="attachment">
                            <option value="None">None</option>
                            <option value="att.gif">att.gif</option>
                          </select>
                        </div>
                      </div><!--/.row-fluid-->
                      <button type="submit" class="d" id="sendMessage" name="sendMessage">Send Message</button>
                    </fieldset>
                  </form>
                  <?php if (isset($results[IMMNController::RESULT_SEND_MSG])) { ?>
                    <div class="alert alert-success">
                      <strong>SUCCESS:</strong><br><?php echo htmlspecialchars($results[IMMNController::RESULT_SEND_MSG]); ?>
                    </div>
                  <?php } ?>
                  <?php if (isset($errors[IMMNController::ERROR_SEND_MSG])) { ?>
                    <div class="alert alert-error">  
                      <strong>ERROR:</strong><br><?php echo htmlspecialchars($errors[IMMNController::ERROR_SEND_MSG]); ?>
                    </div> 
                  <?php } ?>
                </div> <!-- end of sendMessages -->
                <div class="tab-pane fade" id="getMessageHeaders">
                  <label class="note">To use this feature, you must be a subscriber to My AT&amp;T Messages.</label>
                  <form method="post" action="index.php" name="msgHeaderForm" id="msgHeaderForm">
                    <fieldset>
                      <div class="row-fluid">
                        <div class="span4">
                          <input name="headerCountTextBox" type="text" maxlength="3" placeholder="Header Counter" />     
                        </div>
                        <div class="span4">
                          <input name="indexCursorTextBox" type="text" maxlength="30" placeholder="Index Cursor" />     
                        </div>
                        <div class="span4">
                          <button type="submit" class="submit btn btn-primary" name="getMessageHeaders" 
                              id="getMessageHeaders">Get Message Headers</button>
                        </div>
                      </div><!--/row-fluid-->
                    </fieldset>
                  </form><!--/msgHeaderForm-->
                  <div class="alert alert-success">  
                    <strong>Success:</strong><br>Imagine an API response here :)
                  </div> 
                </div><!--/getMessageHeaders-->
                <div class="tab-pane fade" id="getMessageContent">
                  <label class="note">To use this feature, you must be a subscriber to My AT&amp;T Messages.</label>
                  <form method="post" action="index.php" name="msgContentForm" id="msgContentForm">
                    <fieldset>
                      <div class="row-fluid">
                        <div class="span4">
                          <input name="MessageId" id="MessageId" type="text" maxlength="30" placeholder="Message ID" />     
                        </div>
                        <div class="span4">
                          <input name="PartNumber" id="PartNumber" type="text" maxlength="30" placeholder="Part Number" />     
                        </div>
                        <div class="span4">
                        <button type="submit" class="submit btn btn-primary" name="getMessageContent" 
                            id="getMessageContent">Get Message Content</button>
                        </div>
                      </div><!--/row-fluid-->
                    </fieldset>
                  </form>

                  <div class="alert alert-success">  
                    <strong>Success:</strong><br>Imagine an API response here :)
                  </div> 
                  <!--
                  <table class="kvp" id="kvp">
                    <thead>
                      <tr>
                        <th>MessageId</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Received</th>
                        <th>Text</th>
                        <th>Favorite</th>
                        <th>Read</th>
                        <th>Type</th>
                        <th>Direction</th>
                        <th>Contents</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr id="row0">
                        <td data-value="MessageId">WU23435</td>
                        <td data-value="From">+14258028620</td>
                        <td data-value="To">+14257850159,developer@att.com</td>
                        <td data-value="Received">2012-01-14T12:00:00</td>
                        <td data-value="Text">This is a Group MMS text only message</td>
                        <td data-value="Favorite">true</td>
                        <td data-value="Read">False</td>
                        <td data-value="Type">SMSTEXT</td>
                        <td data-value="Direction">IN</td>
                        <td data-value="Contents">&#45;</td>
                      </tr>
                      
                      <tr id="row1">
                        <td data-value="MessageId">WU123</td>
                        <td data-value="From">+14258028620</td>
                        <td data-value="To">+14257850159</td>
                        <td data-value="Received">2012-01-14T12:01:00</td>
                        <td data-value="Text">This is a sms message</td>
                        <td data-value="Favorite">true</td>
                        <td data-value="Read">False</td>
                        <td data-value="Type">SMSTEXT</td>
                        <td data-value="Direction">IN</td>
                        <td data-value="Contents">&#45;</td>
                      </tr>
                      <tr id="row2">
                        <td data-value="MessageId">WU124</td>
                        <td data-value="From">+14257850159</td>
                        <td data-value="To">+14257850159,developer@att.com</td>
                        <td data-value="Received">2012-01-14T12:00:00</td>
                        <td data-value="Text">&#45;</td>
                        <td data-value="Favorite">true</td>
                        <td data-value="Read">False</td>
                        <td data-value="Type">MMS</td>
                        <td data-value="Direction">OUT</td>
                        <td data-value="Contents">
                          <select id="attachments" onchange='chooseSelect("row2", this)'>
                            <option>More..</option>
                            <option>0-part1.txt-text/plain</option>
                            <option>1-sunset.jpg-image/jpeg</option>
                          </select>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  -->
                </div> <!-- end of getMessageContent -->
              </div> <!--/myTab-->
            </div> <!--/wrapper-->
          </div> <!--./bwrapper-->
        </div> <!-- end of span offset -->
      </div> <!--/row-fluid-->
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
