SpeechArea
==========

SpeechArea

This is the repository for the project to create a HTML5 page to capture the speech in a text box, then append that to the text in a Text Area.

Requirements:
 1. The Speech will be captured and converted to text. AT&Ts Speech-To-Text API will be used for this.
 2. The text wil be appended to the text in a Text Area.
 3. User can then edit the text in Text Area using the native key board.
 4. The consolidated text from the TextArea can be posted on social website, such as, a tweet to twitter (later on facebook, google+, tumblr, linkedin etc.)


Technical considerations:
 1. The SpeechArea would work on Android 4.1 native browser and Chrome/firefox desktop browser.
 2. The social network authentication will be handled by individual social site.
   a. Challenge would be figure out the api for each individual channel where SpeechArea text could be posted. This would be an incremental development.
