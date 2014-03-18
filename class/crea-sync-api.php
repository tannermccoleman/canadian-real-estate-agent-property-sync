<?php

require_once dirname(__FILE__) . '/../libraries/phrets.php';

class creasync_api {

    private $loginURL = 'http://sample.data.crea.ca/Login.svc/Login';
    private $userId = 'CXLHfDVrziCfvwgCuL8nUahC';
    private $pass = 'mFqMsCSPdnb5WO1gpEEtDCHH';
    private $templateLocation = "wp-content/plugins/creasync/template/";
    private $adapter;

    public function creasync_api() {
        $this->adapter = new phRETS();
        $this->adapter->SetParam('catch_last_response', true);
        $this->adapter->SetParam('compression_enabled', true);
        $this->adapter->SetParam('disable_follow_location', true);

        $cookie_file = 'creasync';
        @touch(cookie_file);
        if (is_writable(cookie_file)) {
            $this->adapter->SetParam('cookie_file', 'creasync');
        }

        $this->adapter->AddHeader('RETS-Version', 'RETS/1.7.2');
        $this->adapter->AddHeader('Accept', '/');

        $this->loginURL = get_option("creasync_environment_url", "http://sample.data.crea.ca/Login.svc/Login");
        $this->userId = get_option("creasync_api_username", "CXLHfDVrziCfvwgCuL8nUahC");
        $this->pass = get_option("creasync_api_password", "mFqMsCSPdnb5WO1gpEEtDCHH");
        $this->templateLocation = get_option("sc-template", "wp-content/plugins/crea-sync/template/");
    }

    function connect() {
        $connect = $this->adapter->Connect($this->loginURL, $this->userId, $this->pass);

        if ($connect === true) {
            $this->messagePrint('Connection Successful');
        } else {
            $this->displayLog('Connection FAILED');
            if ($error = $this->adapter->Error()) {
                $this->displayLog('ERROR type [' . $error['type'] . '] code [' . $error['code'] . '] text [' . $error['text'] . ']');
            }
            return false;
        }
        return true;
    }

    public function connectionTest() {
        echo "<br/>";
        echo " <div id='poststuff'><div class='postbox' style='width: 98%;'>
		<h3 class='hndle'>Conection Test Log</h3>
		<div class='inside export-target'>
		 <table class='' >
		  <tbody>";
        echo '<tr><td >' . $this->displaylog('Login: ' . $this->loginURL) . '</td></tr>';
        echo '<tr><td >' . $this->displaylog('UserId: ' . $this->userId) . '</td></tr>';
        echo '<tr><td >' . $this->displaylog('Server Details: ' . implode($this->adapter->GetServerInformation())) . '</td></tr>';
        echo '<tr><td >' . $this->displaylog('RETS version: ' . $this->adapter->GetServerVersion()) . '</td></tr>';
        echo '<tr><td >' . $this->displaylog('Firewall: ' . $this->firewalltest()) . '</td></tr>';
        echo "</tbody></table></div></div>";
        echo "<br/><br/>";
        echo '<div class="clear"></div>';
    }

    public function logtypeinfo() {
        $this->displaylog(var_export($this->adapter->GetMetadataTypes(), true));
        $this->displaylog(var_export($this->adapter->GetMetadataResources(), true));

        $this->displaylog(var_dump($this->adapter->GetMetadataClasses("Property")));
        $this->displaylog(var_dump($this->adapter->GetMetadataClasses("Office")));
        $this->displaylog(var_dump($this->adapter->GetMetadataClasses("Agent")));

        $this->displaylog(var_dump($this->adapter->GetMetadataTable("Property", "Property")));
        $this->displaylog(var_dump($this->adapter->GetMetadataTable("Office", "Office")));
        $this->displaylog(var_dump($this->adapter->GetMetadataTable("Agent", "Agent")));

        $this->displaylog(var_dump($this->adapter->GetAllLookupValues("Property")));
        $this->displaylog(var_dump($this->adapter->GetAllLookupValues("Office")));
        $this->displaylog(var_dump($this->adapter->GetAllLookupValues("Agent")));

        $this->displaylog(var_dump($this->adapter->GetMetadataObjects("Property")));
        $this->displaylog(var_dump($this->adapter->GetMetadataObjects("Office")));
        $this->displaylog(var_dump($this->adapter->GetMetadataObjects("Agent")));
    }

    public function searchresidentialproperty($crit, $template, $culture) {
        $render = 'Listing not found.';

        if ($culture == '') {
            $culture = "en-CA";
        }

        $results = $this->adapter->SearchQuery("Property", "Property", $crit, array("Limit" => 1, "Culture" => $culture));

        while ($rets = $this->adapter->FetchRow($results)) {

            if ($template == '') {
                foreach ($rets as $key => &$val) {
                    if ($val != NULL) {
                        $render .= $key . ":" . $val . "<br>";
                    }
                }
            } else {
                $render = file_get_contents($this->templateLocation . $template);
                eval("\$render = \"$render\";");
            }
        }

        $this->adapter->FreeResult($results);

        return $render;
    }

    public function getpropertyobject($id, $type) {
        $record = $this->adapter->GetObject("Property", $type, $id);

        //We won't log this due to data size potential (could be a large image)
        //$this->DisplayLog(var_dump($record));		
        //$this->debug(false);
    }

    public function debug($logResponse = true) {
        if ($last_request = $this->adapter->LastRequest()) {
            $this->displaylog('Reply Code ' . $last_request['ReplyCode'] . ' [' . $last_request['ReplyText'] . ']');
        }
        $this->displaylog('LastRequestURL: ' . $this->adapter->LastRequestURL() . PHP_EOL);

        if ($logResponse) {
            $this->displaylog($this->adapter->GetLastServerResponse());
        }
    }

    public function disconnect() {
        $this->adapter->Disconnect();
    }

    private function displaylog($text) {
        echo $text . "<br>";
    }

    private function messagePrint($message) {
        echo '<div id="setting-error-settings_updated" class="updated settings-error"> 
<p><strong>' . $message . '</strong></p></div>';
    }

    private function firewalltestconn($hostname, $port = 443) {

        $fp = @fsockopen($hostname, $port, $errno, $errstr, 5);

        if (!$fp) {
            echo "Firewall Test: {$hostname}:{$port} FAILED<br>\n";
            return false;
        } else {
            @fclose($fp);
            echo "Firewall Test: {$hostname}:{$port} GOOD<br>\n";
            return true;
        }
    }

    function firewalltest() {
        //We are testing against crea and maintaing the integretiy of the phrets file.
        //This function is copied from phrest
        $google = $this->firewalltestconn("google.com", 80);
        $crt80 = $this->firewalltestconn("data.crea.ca", 80);
        $flexmls80 = $this->firewalltestconn("sample.data.crea.ca", 80);

        if (!$google && !$crt80 && !$flexmls80) {
            echo "Firewall Result: All tests failed.  Possible causes:";
            echo "<ol>";
            echo "<li>Firewall is blocking your outbound connections</li>";
            echo "<li>You aren't connected to the internet</li>";
            echo "</ol>";
            return false;
        }

        if ($google && $crt80 && $flexmls80) {
            echo "Firewall Result: All tests passed.";
            return true;
        }

        if (!$google || !$crt80 || !$flexmls80) {
            echo "Firewall Result: At least one port 80 test failed.  ";
            echo "Likely cause: One of the test servers might be down.";
            return true;
        }

        echo "Firewall Results: Unable to guess the issue.  See individual test results above.";
        return false;
    }

}
?>