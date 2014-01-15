/*
 * Code taken and modified from: https://github.com/mattdiamond/Recorderjs
 * Release under MIT license.
 */
/*
Copyright © 2013 Matt Diamond

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/


var audioContext = new webkitAudioContext();
var audioInput = null,
    realAudioInput = null,
    inputPoint = null,
    audioRecorder = null;
var rafID = null;
var analyserContext = null;
var canvasWidth, canvasHeight;
var recIndex = 0;

/* TODO:

- offer mono option
- "Monitor input" switch
*/

function saveAudio() {
    audioRecorder.exportWAV( doneEncoding );
}

function convertAudioToText(data) {

    // Call the rest proxy here
/*
	var oReq = new XMLHttpRequest();
	oReq.open("GET", "mock_response.txt", true);
	oReq.responseType = "json";	//"blob";
	oReq.onload = function(oEvent) {
	    // alert(oReq.response);
		var convertedText = oReq.response.Recognition.NBest[0].ResultText;
		var oldText = "";
		if (document.getElementById('textArea1').value != null && document.getElementById('textArea1').value != "") {
			oldText = document.getElementById('textArea1').value + " ";
		}
		var newText = oldText + convertedText;
		document.getElementById('textArea1').value = newText;
	};
	oReq.send();// return converted text
*/
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			hideProgressBar();
			setTableText(xmlhttp.responseText);
			var jsonObj = JSON.parse(xmlhttp.responseText);
			// Problem: Parsing of json works for mock_response.txt but not for proxy response - even though they look same
			// Also tried different parsing and response types
			var convertedText = jsonObj.Recognition.NBest[0].ResultText;
			var oldText = "";
			if (document.getElementById('textArea1').value != null && document.getElementById('textArea1').value != "") {
				oldText = document.getElementById('textArea1').value + " ";
			}
			var newText = oldText + convertedText;
			document.getElementById('textArea1').value = newText;
		} else {
			setProgressBarText('Speech conversion request failed...');
		}
	};
	setProgressBarText('Sending request...');
	xmlhttp.open("GET", "mock_response.txt", true);
	//xmlhttp.open("GET", "speech_proxy.php?ServerFile=BostonCeltics.wav", true); // Can use different ServerFile for testing.
	//xmlhttp.open("GET", "speech_proxy.php?ServerFile=doctors.wav", true); // Can use different ServerFile for testing.
	xmlhttp.send();
}

function drawWave( buffers ) {
    // var canvas = document.getElementById( "wavedisplay" );
    // drawBuffer( canvas.width, canvas.height, canvas.getContext('2d'), buffers[0] );
    var data = buffers[0]; // data has the audio stream
    alert("Audio Stream Length is: " + data.length.toString());

    // Call the rest service here
	convertAudioToText(data);
    // var convertedText = convertAudioToText(data);
    }

function doneEncoding( blob ) {
    Recorder.forceDownload( blob, "myRecording" + ((recIndex<10)?"0":"") + recIndex + ".wav" );
    recIndex++;
}

function toggleRecording( e ) {
    if (e.classList.contains("recording")) {
        // stop recording
        audioRecorder.stop();
        e.classList.remove("recording");
        audioRecorder.getBuffers( drawWave );
    } else {
        // start recording
        if (!audioRecorder)
            return;
        e.classList.add("recording");
        audioRecorder.clear();
        audioRecorder.record();
    }
}

// this is a helper function to force mono for some interfaces that return a stereo channel for a mono source.
// it's not currently used, but probably will be in the future.
function convertToMono( input ) {
    var splitter = audioContext.createChannelSplitter(2);
    var merger = audioContext.createChannelMerger(2);

    input.connect( splitter );
    splitter.connect( merger, 0, 0 );
    splitter.connect( merger, 0, 1 );
    return merger;
}
function toggleMono() {
    if (audioInput != realAudioInput) {
        audioInput.disconnect();
        realAudioInput.disconnect();
        audioInput = realAudioInput;
    } else {
        realAudioInput.disconnect();
        audioInput = convertToMono( realAudioInput );
    }

    audioInput.connect(inputPoint);
}


function cancelAnalyserUpdates() {
    window.webkitCancelAnimationFrame( rafID );
    rafID = null;
}

function updateAnalysers(time) {
    if (!analyserContext) {
        var canvas = document.getElementById("analyser");
        canvasWidth = canvas.width;
        canvasHeight = canvas.height;
        analyserContext = canvas.getContext('2d');
    }

    // analyzer draw code here
    {
        var SPACING = 3;
        var BAR_WIDTH = 1;
        var numBars = Math.round(canvasWidth / SPACING);
        var freqByteData = new Uint8Array(analyserNode.frequencyBinCount);

        analyserNode.getByteFrequencyData(freqByteData); 

        analyserContext.clearRect(0, 0, canvasWidth, canvasHeight);
        analyserContext.fillStyle = '#F6D565';
        analyserContext.lineCap = 'round';
        var multiplier = analyserNode.frequencyBinCount / numBars;

        // Draw rectangle for each frequency bin.
        for (var i = 0; i < numBars; ++i) {
            var magnitude = 0;
            var offset = Math.floor( i * multiplier );
            // gotta sum/average the block, or we miss narrow-bandwidth spikes
            for (var j = 0; j< multiplier; j++)
                magnitude += freqByteData[offset + j];
            magnitude = magnitude / multiplier;
            var magnitude2 = freqByteData[i * multiplier];
            analyserContext.fillStyle = "hsl( " + Math.round((i*360)/numBars) + ", 100%, 50%)";
            analyserContext.fillRect(i * SPACING, canvasHeight, BAR_WIDTH, -magnitude);
        }
    }
    
    rafID = window.webkitRequestAnimationFrame( updateAnalysers );
}

function gotStream(stream) {
    // "inputPoint" is the node to connect your output recording to.
    inputPoint = audioContext.createGainNode();

    // Create an AudioNode from the stream.
    realAudioInput = audioContext.createMediaStreamSource(stream);
    audioInput = realAudioInput;
    audioInput.connect(inputPoint);

//    audioInput = convertToMono( input );

    analyserNode = audioContext.createAnalyser();
    analyserNode.fftSize = 2048;
    inputPoint.connect( analyserNode );

    audioRecorder = new Recorder( inputPoint );

    zeroGain = audioContext.createGainNode();
    zeroGain.gain.value = 0.0;
    inputPoint.connect( zeroGain );
    zeroGain.connect( audioContext.destination );
    updateAnalysers();
}

function initAudio() {
    navigator.getMedia = ( 
        navigator.getUserMedia ||
        navigator.mozGetUserMedia ||
        navigator.webkitGetUserMedia ||
        navigator.msGetUserMedia
    );
    if (!navigator.getMedia)
        alert("Error: getUserMedia not supported!");

    navigator.getMedia({audio:true}, gotStream, function(e) {
        alert('Error getting audio');
        console.log(e);
    });
}

window.addEventListener('load', initAudio );
