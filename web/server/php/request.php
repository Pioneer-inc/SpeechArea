<?php
require_once dirname(__FILE__) . '/lib/OAuth/OAuthTokenService.php'; 
require_once dirname(__FILE__) . '/lib/Speech/SpeechService.php'; 
require 'config_speech.php';

$proxyHost = isset($proxy_host) ? $proxy_host : null;
$proxyPort = isset($proxy_port) ? $proxy_port : -1;
$trustAllCerts = isset($accept_all_certs) ? $accept_all_certs : false;
RESTFulRequest::setDefaultProxy($proxyHost, $proxyPort);
RESTFulRequest::setDefaultAcceptAllCerts($trustAllCerts);

function getFileToken($tname) 
{
    require 'config_speech.php';
    $token = OAuthToken::loadToken($tname);
    if ($token == null || $token->isAccessTokenExpired()) {
        $tokenSrvc = new OAuthTokenService($FQDN, $api_key, $secret_key);
        $token = $tokenSrvc->getTokenUsingScope($scope);
        $token->saveToken($tname);
    }

    return $token;
}
$token = getFileToken('t.php');

$srvc = new SpeechService($FQDN, $token);
$fname = 'speech.wav';
$response = $srvc->speechToText($fname, 'Generic');
?>

<div class="row-fluid">
  <div class="span12">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Parameter</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="center"><em><?php echo 'ResponseID'; ?></em></td>
                <td align="center"><em><?php echo $response->getResponseId(); ?></em></td>
            </tr>
            <tr>
                <td align="center"><em><?php echo 'Status'; ?></em></td>
                <td align="center"><em><?php echo $response->getStatus(); ?></em></td>
            </tr>
                <?php 
                $nbest = $response->getNBest();
                if ($nbest != NULL) { ?>

            <tr>
                <td align="center"><em><?php echo 'Hypothesis'; ?></em></td>
                <td align="center"><em><?php echo $nbest->getHypothesis(); ?></em></td>
            </tr>
            <tr>
                <td align="center"><em><?php echo 'LanguageId'; ?></em></td>
                <td align="center"><em><?php echo $nbest->getLanguageId(); ?></em></td>
            </tr>
            <tr>
                <td align="center"><em><?php echo 'Confidence'; ?></em></td>
                <td align="center"><em><?php echo $nbest->getConfidence(); ?></em></td>
            </tr>
            <tr>
                <td align="center"><em><?php echo 'Grade'; ?></em></td>
                <td align="center"><em><?php echo $nbest->getGrade(); ?></em></td>
            </tr>
            <tr>
                <td align="center"><em><?php echo 'ResultText'; ?></em></td>
                <td align="center"><em><?php echo $nbest->getResultText(); ?></em></td>
            </tr>
            <tr>
                <td align="center"><em><?php echo 'Words'; ?></em></td>
                <td align="center">
                    <em><?php echo json_encode($nbest->getWords()); ?></em>
                </td>
            </tr>
            <tr>
                <td class="cell" align="center"><em><?php echo 'WordScores'; ?></em></td>
                <td class="cell" align="center">
                    <em><?php echo json_encode($nbest->getWordScores()); ?></em>
                </td>
            </tr>
                <?php } ?>
        </tbody>
    </table>
  </div><!--./span12-->
</div><!--./row-fluid-->	
