<?php
require ("config_constants.php");
require ("envProperties.php");

if ($_POST['order_num'])
    {
    $critical_error = 0;
    $order_num = $_POST['order_num'];
    $url = $papaUrl . $order_num;
    $json = file_get_contents($url);
    $json_output = json_decode($json);
    if ($json_output)
        {
        $acct_num = $json_output->account_id;
        $acct_name = $json_output->account_name;
        $access_type = $json_output->access_type;
        $call_agent = $json_output->call_agent;
        $ca_type = $json_output->ca_type;
        $voice_service_type = $json_output->voice_service_type;
        $aggregation_router = $json_output->aggregation_router;
        $committed_bw = $json_output->committed_bw;
        $btn = $json_output->btn;
        $efm_switch = $json_output->efm_switch;
        $hsl_bits = explode("-", $json_output->hsl);
        $vlan_bits = explode(".", $json_output->vlan);
        $local_line_count = 0;
        foreach($json_output->local_lines as $local_line)
            {
            if ($local_line->port > 0)
                {
                $port = $local_line->port;
                $local_line_tn[$port] = $local_line->tn;
                $local_line_count++;
                }
            }

        $network_count = 0;
        foreach($json_output->networks as $network)
            {
            $network_count++;
            $network_type[$network_count] = $network->type;
            $network_description[$network_count] = $network->description;
            $network_addr[$network_count] = $network->addr;
            $network_cidr[$network_count] = $network->cidr;
            $network_iad[$network_count] = $network->iad;
            }

        $circ_count = 0;
        foreach($json_output->circs as $circ)
            {
            $circ_count++;
            $circ_cid[$circ_count] = $circ->cid;
            $circ_cfa[$circ_count] = $circ->cfa;
            $circ_mlp[$circ_count] = $circ->mlp;
            $circ_mlu[$circ_count] = $circ->mlu;
            $circ_slot[$circ_count] = $circ->slot;
            $circ_subslot[$circ_count] = $circ->subslot;
            $circ_port[$circ_count] = $circ->port;
            $circ_bw[$circ_count] = $circ->port - bw;
            }

        // DETERMINE DEVICE TYPE

        $device = "IAD";
        if ($access_type == "T1" && $circ_count > 3)
            {
            $device = "ISR";
            }

        if ($committed_bw > 10)
            {
            $device = "ISR";
            }

        if ($access_type == "Fiber")
            {
            $device = "ISR";
            }
        } //if json_output
    } //POST['Order_num']
  else
    {
    $critical_error = 1;
    }

if ($_POST['buttonClicked'] == "CB")
    {
    $client = new SoapClient($clientUrl);
    $acct_num = $_POST['acct_num'];
    $deleteAllLocalLinesMethod = "deleteAllLocalLines";
    $result = $client->$deleteAllLocalLinesMethod(array(
        'accountId' => $acct_num
    ));
    $CBresult = $result->return;
    }

if ($_POST['buttonClicked'] == "DB")
    {
    $client = new SoapClient($clientUrl);
    $acct_num = $_POST['acct_num'];
    $disconnectCustomerMethod = "disconnectCustomer";
    $result = $client->$disconnectCustomerMethod(array(
        'accountId' => $acct_num
    ));
    $DBresult = $result->return;
    }

if ($_POST['buttonClicked'] == "CEG" || $_POST['buttonClicked'] == "CB" || $_POST['buttonClicked'] == "DB")
    {
    $acct_num = $_POST['acct_num'];
    $acct_name = $_POST['acct_name'];
    $iadname = $_POST['iad_name'];
    $device = $_POST['device'];
    $tenk_name = $_POST['tenk_name'];
    $zip_code = $_POST['zip_code'];
    $post_access = $_POST['access'];
    if ($post_access == "ACCE1")
        {
        $access_type = "T1";
        }
      else
    if ($post_access == "ACCE2")
        {
        $access_type = "EFM";
        }
      else
    if ($post_access == "ACCE3")
        {
        $access_type = "Fiber";
        }

    $call_agent = $_POST['call_agent'];
    $ca_type = $_POST['ca_type'];
    $voice_service_type = $_POST['voice_type'];
    $aggregation_router = $_POST['aggregation_router'];
    $committed_bw = $_POST['committed_bw'];
    $btn = $_POST['btn'];
    $efm_switch = $_POST['EFM_SWITCH'];
    $hsl_bits = $_POST['HSL_EFM'];
    $vlan_bits = $_POST['VLAN_EFM'];
    if ($_POST['voice_type'] == "VOIC1")
        {
        $local_line_count = $_POST['num_analog_lines_ANALOG'];
        $local_line_count = substr($local_line_count, 4);
        }

    if ($_POST['voice_type'] == "VOIC4")
        {
        $local_line_count = $_POST['num_analog_lines_SIP'];
        $local_line_count = substr($local_line_count, 4);
        }

    if ($_POST['voice_type'] == "VOIC5")
        {
        $local_line_count = $_POST['num_analog_lines_VOPRI'];
        $local_line_count = substr($local_line_count, 4);
        }

    if ($_POST['voice_type'] == "VOIC6")
        {
        $local_line_count = $_POST['num_analog_lines_PRIMIX'];
        $local_line_count = substr($local_line_count, 4);
        }

    for ($i = 1; $i <= $local_line_count; $i++)
        {
        $local_line_tn[$i] = $_POST['ANALOG' . $i];
        }
    }

if ($_POST['buttonClicked'] == "CEG")
    {
    $client = new SoapClient($clientUrl);
    $market = $_POST['call_agent'];
    $bwns_profile = $_POST['bwns_profile'];
    $call_capacity = $_POST['call_capacity'];
    if ($voice_service_type == "VOIC1")
        {
        $voice_type = "Analog";
        }
      else
    if ($voice_service_type == "VOIC2")
        {
        $voice_type = "EnhancedDigital";
        }
      else
    if ($voice_service_type == "VOIC4")
        {
        $voice_type = "SipConnect";
        }
      else
    if ($voice_service_type == "VOIC5")
        {
        $voice_type = "VoiceOnlyPRI";
        }
      else
    if ($voice_service_type == "VOIC6")
        {
        $voice_type = "Multinet";
        }

    if ($market == "MARK1")
        {
        $market_name = "Houston - TX";
        }
      else
    if ($market == "MARK2")
        {
        $market_name = "Atlanta - GA";
        }
      else
    if ($market == "MARK3")
        {
        $market_name = "Miami - FL";
        }
      else
    if ($market == "MARK4")
        {
        $market_name = "Washington - DC";
        }
      else
    if ($market == "MARK5")
        {
        $market_name = "Dallas - TX";
        }
      else
    if ($market == "MARK6")
        {
        $market_name = "Denver - CO";
        }
      else
    if ($market == "MARK7")
        {
        $market_name = "Los Angeles - CA";
        }
      else
    if ($market == "MARK8")
        {
        $market_name = "San Francisco - CA";
        }
      else
    if ($market == "MARK9")
        {
        $market_name = "Seattle - WA";
        }
      else
    if ($market == "MARK10")
        {
        $market_name = "Chicago - IL";
        }
      else
    if ($market == "MARK11")
        {
        $market_name = "Detroit - MI";
        }
      else
    if ($market == "MARK12")
        {
        $market_name = "Minneapolis";
        }
      else
    if ($market == "MARK13")
        {
        $market_name = "Boston";
        }
      else
    if ($market == "MARK14")
        {
        $market_name = "San Diego - CA";
        }

    $method = "provision" . $voice_type . "Customer";
    if ($voice_type == "Analog")
        {
        $provisionCEGResult = $client->$method(array(
            'accountId' => $acct_num,
            'companyName' => $acct_name,
            'market' => $market_name,
            'bwnsProfile' => strtoupper($bwns_profile)
        ));
        }
      else
        {
        $provisionCEGResult = $client->$method(array(
            'accountId' => $acct_num,
            'companyName' => $acct_name,
            'market' => $market_name,
            'bwnsProfile' => strtoupper($bwns_profile) ,
            'initialCallCapacity' => $call_capacity
        ));
        }

    $return = $provisionCEGResult->return;
	//echo "Request: ".$client->__getLastRequest();
    //var_dump($provisionCEGResult->return);

    /*    echo "<br /><br />";
    echo "<b>You submitted:</b> $voice_type, $acct_num, $acct_name, $market_name, $bwns_profile, $call_capacity<br /><br />";
    echo "<b>Method called:</b> " . $method . "<br /><br />";
    echo "<b>Status:</b> " . $return->status . "<br /><br />";
    echo "<br /><br />";*/
    }

if ($json_output || $_POST['buttonClicked'] == "CEG" || $_POST['buttonClicked'] == "CB" || $_POST['buttonClicked'] == "DB")
    {
    $bsm_error = 0;
    $bsm_client = new SoapClient($clientUrl);
    try
        {
        if (!$bsm_result = $bsm_client->findCustomerLocation(array(
            'accountId' => $acct_num
        )))
            {
            throw new Exception('Could not locate account in Broadsoft');
            }

        $broadsoft = $bsm_result->return;
        }

    catch(Exception $fault)
        {
        $bsm_error = 1;
        }

    $agg_router_bits = explode(".", $aggregation_router);
    $tenk_name = $agg_router_bits[0];
    $market = substr($agg_router_bits[1], 0, 3);
    $hsl = ltrim($hsl_bits[1], "0");
    $vlan = $vlan_bits[1];
    if (substr($vlan_bits[0], 0, 1) == 0)
        {
        $tenk_interface = "GigabitEthernet" . substr($vlan_bits[0], 1);
        }
      else
        {
        $tenk_interface = "Port-channel" . $vlan_bits[0];
        }

    if ($json_output->db_error)
        {
        echo $json_output->db_error . "<br />";
        }

    if ($json_output->missing)
        {
        echo $json_output->missing . "<br />";
        }

    // BUILD SERIAL INTERFACE ARRAY FOR T1 ACCESS and calculate bandwidth

    if ($access_type == "T1")
        {
        for ($i = 1; $i <= $circ_count; $i++)
            {

            // DEFINE SLOT

            if ($circ_slot[$i] == "00")
                {
                $slot = 0;
                }
            elseif (strlen($circ_slot[$i]) > 1 && substr($circ_slot[$i], 0, 1) == 0)
                {
                $slot = ltrim($circ_slot[$i], "0");
                }
              else
                {
                $slot = $circ_slot[$i];
                }

            // DEFINE SUBSLOT (IF PRESENT)

            if (preg_match('/-/', $circ_subslot[$i]))
                {
                $bits = explode("-", $circ_subslot[$i]);
                $subslot = $bits[1];
                }
            elseif (strlen($circ_subslot[$i]) > 0)
                {
                if (strlen($circ_subslot[$i]) > 1 && substr($circ_subslot[$i], 0, 1) == 0)
                    {
                    $subslot = ltrim($bit, "0");
                    }
                  else
                    {
                    $subslot = $circ_subslot[$i];
                    }
                }

            // DEFINE PORT

            $port_bits = explode("/", $circ_port[$i]);

            // LOAD INTERFACE NAME INTO SERIAL ARRAY

            $serial[$i] = "Serial" . $slot;
            if (strlen($subslot) > 0)
                {
                $serial[$i] = $serial[$i] . "/" . $subslot;
                }

            foreach($port_bits as $bit)
                {
                if (strlen($bit) > 1 && substr($bit, 0, 1) == 0)
                    {
                    $bit = ltrim($bit, "0");
                    }

                $serial[$i] = $serial[$i] . "/" . $bit;
                }

            $serial[$i] = $serial[$i] . ":0";
            $slot = NULL;
            $subslot = NULL;
            $port_bits = NULL;
            }

        $calc_bw = $circ_count * 1.5;
        }

    // RUN FIBER ACCESS OPERATIONS

    if ($access_type == "Fiber")
        {
        if ($tenk_name == $dark_fiber_10K_pairs[$market][0])
            { //LOOKUP SECONDARY 10K NAME
            $secondary_tenk_name = $dark_fiber_10K_pairs[$market][1];
            }
          else
            {
            $secondary_tenk_name = $dark_fiber_10K_pairs[$market][0];
            }

        if (strpos($circ_cid[1], $acct_num))
            {

            // CONNECT TO MYSQL

            $connection = @mysql_connect($mysqladd, $mysqluser, $mysqlpass) or die(mysql_error());
            $database = @mysql_select_db($databasename, $connection) or die(mysql_error());
            $cid_parts = explode("/", $circ_cid[1]);
            $siteID = $cid_parts[2];
            $vlanQuery = "SELECT VLAN FROM fiberBldgData WHERE siteID = '$siteID'";
            $vlanQueryResult = mysql_query($vlanQuery, $connection);
            while ($row = mysql_fetch_array($vlanQueryResult))
                {
                $site_vlan = $row['VLAN'];
                }
            }
        }

    // EXTRACT IP AND IAD NAME FROM NETWORKS DATA

    $set_public = 0;
    $set_private = 0;
    for ($i = 1; $i <= $network_count; $i++)
        {
        if (substr($network_description[$i], 0, 9) != "DELETE ME")
            {
            if ($access_type == "Fiber" && $network_type[$i] == "CBEYOND - FIBER" && $network_iad[$i] != "" && $set_private == 0)
                {
                $iadname_bits = explode(".", $network_iad[$i]);
                $iadname = STRTOUPPER($iadname_bits[0]);
                $iad_cidr = $network_cidr[$i];
                $set_private = 1;
                }

            if ($access_type == "T1" && $network_type[$i] == "CBEYOND - PRIVATE" && $network_iad[$i] != "" && $set_private == 0)
                {
                $iadname_bits = explode(".", $network_iad[$i]);
                $iadname = STRTOUPPER($iadname_bits[0]);
                $iad_cidr = $network_cidr[$i];
                $set_private = 1;
                }

            if ($access_type == "EFM" && $network_type[$i] == "CBEYOND - PRIVATE" && $network_iad[$i] != "" && $set_private == 0)
                {
                $iadname_bits = explode(".", $network_iad[$i]);
                $iadname = STRTOUPPER($iadname_bits[0]);
                $iad_cidr = $network_cidr[$i];
                $set_private = 1;
                }

            if ($network_type[$i] == "CBEYOND - PUBLIC" && $set_public == 0)
                {
                $public_ip = $network_addr[$i];
                $public_cidr = $network_cidr[$i];
                $set_public = 1;
                }

            if ($network_type[$i] == "CBEYOND - LOOPBACK" && $set_public == 0)
                {
                $public_ip = $network_addr[$i];
                $public_cidr = $network_cidr[$i];
                $set_public = 1;
                }

            if ($network_type[$i] == "CBEYOND - EFM")
                {
                $efm_ip = $network_addr[$i];
                $efm_cidr = $network_cidr[$i];
                }
            }
        }

    // BUILD MLP ARRAY FOR EFM ACCESS

    if ($access_type == "EFM")
        {
        for ($i = 1; $i <= $circ_count; $i++)
            {
            $mlp[$i] = $circ_mlp[$i];
            $efm_cid[$i] = $circ_cid[$i];
            }
        }
    }

function highlight_select($field)
    {
    /*
    if($_POST['order_num']){
    if($field == NULL){
    echo "style=\"background-color:#B20000;color:white;\"";
    }
    }

    */
    }

function highlight_input($field)
    {
    if ($_POST['order_num'])
        {
        if ($field)
            {
            echo "value=\"$field\"";
            }
        }
    }

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd"> 

<html>

<head>
<title> Service Activations - ReDCON</title>

<!--[if IE]>
<link rel="stylesheet" type="text/css" href="../style/sardoss_style_ie.css" />
<![endif]-->

<!--[if !IE]><!-->
<link rel="stylesheet" type="text/css" href="../style/sardoss_style.css" />
<!--<![endif]-->

</head>

<script type="text/javascript">
var numberRegExp = /^[0-9]*$/;
var errorFieldArray = new Array();

function show(obj)
{
    fullID = obj.options[obj.selectedIndex].value;
    no = fullID.substr(4);
    ident = fullID.substr(0, 4);
    count = obj.options.length;

    for (i = 1; i < count; i++) {
        document.getElementById(ident + i).style.display = 'none';
    }
    if (no > 0) {
        document.getElementById(ident + no).style.display = 'block';
    }
}

function multi_show(obj)
{
    fullID = obj.options[obj.selectedIndex].value;
    no = fullID.substr(4);
    ident = fullID.substr(0, 4);
    count = obj.options.length;
    current = 1;

    for (i = 1; i <= no; i++) {
        document.getElementById(ident + i).style.display = 'block';
        current++;
    }
    for (i = current; i < count; i++) {
        document.getElementById(ident + i).style.display = 'none';
    }
}

function show_confirm(config_type)
{
    document.f1.action = "config_output.php";
    document.f1.target = "new_window";

    var Missing = "";
    var Msg = "";
    var errorFieldIndex = 0;

    resetErrorFields(errorFieldArray);
    errorFieldArray = new Array();

    //check for NULL values
    if (document.f1.num_devices.value.substring(4) == "0") {
        errorFieldArray[errorFieldIndex++] = document.f1.num_devices;
        Missing = Missing + "Number of Devices\n";
    }
    if (document.f1.access.value.substring(4) == "0") {
        errorFieldArray[errorFieldIndex++] = document.f1.access;
        Missing = Missing + "Access Type\n";
    }
    if (document.f1.voice_type.value.substring(4) == "0") {
        errorFieldArray[errorFieldIndex++] = document.f1.voice_type;
        Missing = Missing + "Voice Service Type\n";
    }
    if (document.f1.device.value.substring(4) == "0") {
        errorFieldArray[errorFieldIndex++] = document.f1.device;
        Missing = Missing + "Device Type\n";
    }
    if (document.f1.acct_num.value == "") {
        errorFieldArray[errorFieldIndex++] = document.f1.acct_num;
        Missing = Missing + "Account Number\n";
    }
    if (document.f1.acct_name.value == "") {
        errorFieldArray[errorFieldIndex++] = document.f1.acct_name;
        Missing = Missing + "Account Name\n";
    }
	if (document.f1.iad_cidr.value == "") {
        errorFieldArray[errorFieldIndex++] = document.f1.iad_cidr;
        Missing = Missing + "IAD Cidr\n";
    }
    if (document.f1.tenk_name.value == "") {
        errorFieldArray[errorFieldIndex++] = document.f1.tenk_name;
        Missing = Missing + "10K Name\n";
    }
    if (document.f1.iad_name.value == "") {
        errorFieldArray[errorFieldIndex++] = document.f1.iad_name;
        Missing = Missing + "IAD Name\n";
    }
    if (document.f1.public_ip.value == "" && document.f1.voice_type.value != "VOIC5") {
        errorFieldArray[errorFieldIndex++] = document.f1.public_ip;
        Missing = Missing + "Public IP\n";
    }
    if (document.f1.public_subnet.value == "" && document.f1.voice_type.value != "VOIC5") {
        errorFieldArray[errorFieldIndex++] = document.f1.public_subnet;
        Missing = Missing + "Subnet Mask\n";
    }
	
    if (config_type == "activate" && document.f1.voice_type.value != "VOIC7") {
        if (document.f1.BWAS.value == "") {
            errorFieldArray[errorFieldIndex++] = document.f1.BWAS;
            Missing = Missing + "BWAS\n";
        }
    }
    //MISSING CRITICAL VALUES ALERT
    if (Missing != "")
	{
        Missing = "No values provided for the following field(s):\n\n" + Missing;
        alert(Missing);
        if (errorFieldArray[0])
            focusErrorFields(errorFieldArray);
        return false;
    }

    //VERIFY ANALOG SERVICE IF NUMBER OF DEVICES IS 2 
    if (document.f1.num_devices.value.substring(4) == "2" && document.f1.voice_type.value.substring(4) != "1")
	{
        alert("ERROR: Dual-devices is only supported for analog service at this time.\nAll other services should be configured on a single device.")
        return false;
    }

    //check account number format
    /*	var acct_num = document.forms[0].acct_num.value;
    	var acct_num_pattern = /^[0-9]{4,6}$/;
    	if(acct_num.match(acct_num_pattern) == null){
    		alert("ERROR! - Account number is invalid. \n\n Please double-check the account number.")
    		return false;
    	}
    	*/
    //check ip address format
    var public_ip = document.f1.public_ip.value;
    var public_ip_pattern = /^[1-9][0-9]{0,2}\.[0-9][0-9]{0,2}\.[0-9][0-9]{0,2}\.[0-9][0-9]{0,2}$/;
    if (public_ip.match(public_ip_pattern) == null && document.f1.voice_type.value != "VOIC5")
	{
        alert("ERROR! - Public IP is invalid. \n\n Please double-check the public IP.")
        return false;
    }

    if (document.f1.access.value.substring(4) == "1")
	{
        var serial1 = document.f1.SERIAL1.value.toUpperCase();
        var serial2 = document.f1.SERIAL2.value.toUpperCase();
        var serial3 = document.f1.SERIAL3.value.toUpperCase();
        var serial4 = document.f1.SERIAL4.value.toUpperCase();
        var serial5 = document.f1.SERIAL5.value.toUpperCase();
        var serial6 = document.f1.SERIAL6.value.toUpperCase();
        var serial7 = document.f1.SERIAL7.value.toUpperCase();
        var serial8 = document.f1.SERIAL8.value.toUpperCase();
        var serial_int_pattern = /S(E|ER|ERI|ERIA|ERIAL)*[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{1,2}:0/i;

        //check serial interface format
        if (serial1 != "" && serial1.match(serial_int_pattern) == null) {
            alert("ERROR! - One or more serial interfaces don't look right. \n\nPlease double-check the format of the serial interface data.")
            return false;
        }
        if (serial2 != "" && serial2.match(serial_int_pattern) == null) {
            alert("ERROR! - One or more serial interfaces don't look right. \n\nPlease double-check the format of the serial interface data.")
            return false;
        }
        if (serial3 != "" && serial3.match(serial_int_pattern) == null) {
            alert("ERROR! - One or more serial interfaces don't look right. \n\nPlease double-check the format of the serial interface data.")
            return false;
        }
        if (serial4 != "" && serial4.match(serial_int_pattern) == null) {
            alert("ERROR! - One or more serial interfaces don't look right. \n\nPlease double-check the format of the serial interface data.")
            return false;
        }
        if (serial5 != "" && serial5.match(serial_int_pattern) == null) {
            alert("ERROR! - One or more serial interfaces don't look right. \n\nPlease double-check the format of the serial interface data.")
            return false;
        }
        if (serial6 != "" && serial6.match(serial_int_pattern) == null) {
            alert("ERROR! - One or more serial interfaces don't look right. \n\nPlease double-check the format of the serial interface data.")
            return false;
        }
        if (serial7 != "" && serial7.match(serial_int_pattern) == null) {
            alert("ERROR! - One or more serial interfaces don't look right. \n\nPlease double-check the format of the serial interface data.")
            return false;
        }
        if (serial8 != "" && serial8.match(serial_int_pattern) == null) {
            alert("ERROR! - One or more serial interfaces don't look right. \n\nPlease double-check the format of the serial interface data.")
            return false;
        }
    }
	
    window.open('', 'new_window', 'width=800,height=800,location=0,toolbar=0,status=0,menubar=0,resizable=1,scrollbars=1').focus();
}

</script>

<style type="text/css">

td{
	font-size:80%;
	font-weight:bold;
	padding-right:10px;
}

td.form{
	padding-bottom:10px;
}

td.line{
	text-align:right;
	width:50px;
	padding-right:1px;
	vertical-align:0px;
}

input, select{
	background-color:#F2F2F2;
}

</style>

<body onload="show(f1.voice_type); displayCEGResults(); displayCBResults(); displayDBResults();">

	<div class="full_header">

		<div class="titlecolor">
			<div class="title">
				<img style="margin:10px;" src="../images/SA_title_words_small.png">
			</div>

			<div class="pagetitle"><a href="/sardoss">Home</a>
				<span class="location">
				 / <a href="../config/config_gen_order_form.php">ReDCON</a>
				 / <u>Order Data</u>
				</span>
			</div>
		</div>

		<hr class="topline" />

		<?php include 'optionbar.php'; ?>

	</div>

	<div id="nojs" style="text-align:center;position:relative;top:75px;"><br /><br />
		JavaScript must be enabled in your browser in order to use this tool.<br />
		JavaScript is either disabled or not supported by your browser.<br /><br />
		Enable Javascript in your browser options and try again.
	</div>

	<div id="fullpage" style="display:none">

	<div style="min-width:800px;position:relative;top:75px;margin-left:10px;width:100%;">

	<?php

		if($access_type == "T1" && $calc_bw < $committed_bw){
			echo "<span style=\"color:red;\"><strong>ALERT</strong>: Siebel indicates a committed bandwidth of $committed_bw, but the system only pulled enough T1s to provide $calc_bw. Please double-check the number of T1s on the account and make the necessary adjustments below.</span><br /><br />";
		}

		if($json_output->not_found){
			echo "<span style=\"color:red;\"><strong>ALERT</strong>: Data for service order <strong>" . $json_output->not_found . "</strong> could not be found.  You're on your own...</span><br /><br />";
			$critical_error = 1;
		}
		if($json_output->no_accepted_circuits){
			echo "<span style=\"color:red;\"><strong>ALERT</strong>: There are no accepted circuits for service order <strong>" . $json_output->no_accepted_circuits . "</strong>.  You're on your own...</span><br /><br />";
			$critical_error = 1;
		}
		if($json_output->no_service_address){
			echo "<span style=\"color:red;\"><strong>ALERT</strong>: There is no service address for service order <strong>" . $json_output->no_service_address . "</strong>.  You're on your own...</span><br /><br />";
			$critical_error = 1;
		}
		if($ca_type == "BTS"){
			echo "<span style=\"color:red;\"><strong>ALERT</strong>: This appears to be a BTS account, which is not currently supported by this application.</span><br /><br />";
			$critical_error = 1;
		}
		if($bsm_error == 1){
			echo "<span style=\"color:red;\"><strong>ALERT</strong>: Cannot locate the account in Broadsoft.  Please specify the correct Broadsoft server (BWAS) below.</span><br /><br />";
			$critical_error = 1;
		}

	?>
	<br />

<form name="f1"  action="config_output.php" method="POST" >
	<input type="hidden" name ="buttonClicked" value=""/>
	<input type="hidden" name ="BSMResults" value="" />
	<input type="hidden" name ="pca" value="PRCA2" />

	<table width="100%">
	<tbody>
	<tr>
		<td valign="top">
		<table cellspacing="10">
		<tbody>
			<tr>
				<td>
					Number of Devices: &nbsp;&nbsp;
					<select onchange="multi_show(this)" name="num_devices">
						<option value="CPEN0"></option>
						<option value="CPEN1" selected >1</option>
						<option value="CPEN2"  <?php if($_POST['num_devices'] == "CPEN2") echo 'selected'?>>2</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<div id="CPEN1" style="display:block;text-align:left;">
						Primary Device:
						<select <?php highlight_select($device); ?> onchange="show(this)" name="device">
							<option value="DEVC0"></option>
							<option value="DEVC1" <?php if($device == "IAD" || $_POST['device'] == "DEVC1")
								{ echo "selected"; } ?> >IAD</option>
							<option value="DEVC2" <?php if($device == "SPIAD" || $_POST['device'] == "DEVC2")
								{ echo "selected"; } ?> >SPIAD</option>
							<option value="DEVC3" <?php if($device == "ISR" || $_POST['device'] == "DEVC3")
								{ echo "selected"; } ?> >ISR</option>
						</select>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div id="CPEN2" <?php if($_POST['num_devices'] == "CPEN2"){ echo "style=\"display:block\""; }else
						{ echo "style=\"display:none\""; } ?> >
						Second Device: 
						<select onchange="show(this)" name="second_device">
							<option value="SCDV0"></option>
							<option value="SCDV1" <?php if($_POST['second_device'] == "SCDV1")
								{ echo "selected"; }?> >IAD</option>
							<option value="SCDV2" <?php if($_POST['second_device'] == "SCDV2")
								{ echo "selected"; }?> >SPIAD</option>
							<option value="SCDV3" <?php if($_POST['second_device'] == "SCDV3")
								{ echo "selected"; }?> >ISR</option>
						</select>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div id="MAR" style="display:block;text-align:left;">
					Market:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<select style="width: 75px" name="call_agent" <?php highlight_select($call_agent); ?>>
						<option value="MARK0"></option>
						<option value="MARK1" <?php if($call_agent == "HOU" || $_POST['call_agent'] == "MARK1")
							{ echo "selected"; } ?> >HOU</option>
						<option value="MARK2" <?php if($call_agent == "ATL" || $_POST['call_agent'] == "MARK2")
							{ echo "selected"; } ?> >ATL</option>
						<option value="MARK3" <?php if($call_agent == "MIA" || $_POST['call_agent'] == "MARK3")
							{ echo "selected"; } ?> >MIA</option>
						<option value="MARK4" <?php if($call_agent == "DCA" || $_POST['call_agent'] == "MARK4")
							{ echo "selected"; } ?> >DCA</option>
						<option value="MARK5" <?php if($call_agent == "DAL" || $_POST['call_agent'] == "MARK5")
							{ echo "selected"; } ?> >DAL</option>
						<option value="MARK6" <?php if($call_agent == "DEN" || $_POST['call_agent'] == "MARK6")
							{ echo "selected"; } ?> >DEN</option>
						<option value="MARK7" <?php if($call_agent == "LAX" || $_POST['call_agent'] == "MARK7")
							{ echo "selected"; } ?> >LAX</option>
						<option value="MARK8" <?php if($call_agent == "SFO" || $_POST['call_agent'] == "MARK8")	
							{ echo "selected"; } ?> >SFO</option>
						<option value="MARK9" <?php if($call_agent == "SEA" || $_POST['call_agent'] == "MARK9")
							{ echo "selected"; } ?> >SEA</option>
						<option value="MARK10" <?php if($call_agent == "CHI" || $_POST['call_agent'] == "MARK10")
							{ echo "selected"; } ?> >CHI</option>
						<option value="MARK11" <?php if($call_agent == "DET" || $_POST['call_agent'] == "MARK11")
							{ echo "selected"; } ?> >DET</option>
						<option value="MARK12" <?php if($call_agent == "MSP" || $_POST['call_agent'] == "MARK12")
							{ echo "selected"; } ?> >MSP</option>
						<option value="MARK13" <?php if($call_agent == "BOS" || $_POST['call_agent'] == "MARK13")
							{ echo "selected"; } ?> >BOS</option>
						<option value="MARK14" <?php if($call_agent == "SDG" || $_POST['call_agent'] == "MARK14")
							{ echo "selected"; } ?> >SDG</option>
					</select>
					</div>
				</td>
			</tr>
			<tr>
				<td>Zip Code: <input type="text" name="zip_code" size="12" maxlength="5" value="<?=$_POST['zip_code']?>"/>
				</td>
			</tr>
		</tbody>
		</table>
		</td>

		<td valign="top">
			<table cellspacing="7">
			<tbody>
			<tr>
				<td>
				Access Type:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<select id="col2Width" <?php highlight_select($access_type); ?> onchange="show(this)" name="access" >
				<option value="ACCE0"></option>
				<option value="ACCE1" <?php if($access_type == "T1" ){ echo "selected"; } ?> >T1</option>
				<option value="ACCE2" <?php if($access_type == "EFM"){ echo "selected"; } ?> >EFM</option>
				<option value="ACCE3" <?php if($access_type == "Fiber" ){ echo "selected"; } ?> >FIBER/WEFM</option>
				</select>
				</td>
			</tr>
			<tr>
				<td>
				<div id="ACCE1" <?php if($access_type == "T1"){ echo "style=\"display:block\""; }else
					{ echo "style=\"display:none\""; } ?> >
				Number of T1s:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<select id="col2Width" onchange="multi_show(this)" name="num_circuits_T1">
					<option value="TONE0"></option>
					<option value="TONE1" <?php if(($circ_count == 1 && $access_type == "T1") || $_POST['num_circuits_T1'] == "TONE1")
						{ echo "selected"; } ?> >1</option>
					<option value="TONE2" <?php if(($circ_count == 1 && $access_type == "T1") || $_POST['num_circuits_T1'] == "TONE2")
						{ echo "selected"; } ?> >2</option>
					<option value="TONE3" <?php if(($circ_count == 1 && $access_type == "T1") || $_POST['num_circuits_T1'] == "TONE3")
						{ echo "selected"; } ?> >3</option>
					<option value="TONE4" <?php if(($circ_count == 1 && $access_type == "T1") || $_POST['num_circuits_T1'] == "TONE4")
						{ echo "selected"; } ?> >4</option>
					<option value="TONE5" <?php if(($circ_count == 1 && $access_type == "T1") || $_POST['num_circuits_T1'] == "TONE5")
						{ echo "selected"; } ?> >5</option>
					<option value="TONE6" <?php if(($circ_count == 1 && $access_type == "T1") || $_POST['num_circuits_T1'] == "TONE6")
						{ echo "selected"; } ?> >6</option>
					<option value="TONE7" <?php if(($circ_count == 1 && $access_type == "T1") || $_POST['num_circuits_T1'] == "TONE7")
						{ echo "selected"; } ?> >7</option>
					<option value="TONE8" <?php if(($circ_count == 1 && $access_type == "T1") || $_POST['num_circuits_T1'] == "TONE8")
						{ echo "selected"; } ?> >8</option>
				</select>
				</div>
				</td>
			</tr>
			<tr>
				<td>
					<div id="ACCE2" <?php if($access_type == "EFM"){ echo "style=\"display:block\""; }else
						{ echo "style=\"display:none\""; } ?> >
					Number of EFM pairs:&nbsp;
					<select id="col2Width" onchange="multi_show(this)" name="num_circuits_EFM">
					<option value="EFMP0"></option>
					<option value="EFMP2" <?php if($circ_count == 2 && $access_type == "EFM"){ echo "selected"; } ?> >2</option>
					<option value="EFMP3" <?php if($circ_count == 3 && $access_type == "EFM"){ echo "selected"; } ?> >3</option>
					<option value="EFMP4" <?php if($circ_count == 4 && $access_type == "EFM"){ echo "selected"; } ?> >4</option>
					<option value="EFMP5" <?php if($circ_count == 5 && $access_type == "EFM"){ echo "selected"; } ?> >5</option>
					<option value="EFMP6" <?php if($circ_count == 6 && $access_type == "EFM"){ echo "selected"; } ?> >6</option>
					<option value="EFMP7" <?php if($circ_count == 7 && $access_type == "EFM"){ echo "selected"; } ?> >7</option>
					<option value="EFMP8" <?php if($circ_count == 8 && $access_type == "EFM"){ echo "selected"; } ?> >8</option>
					</select>
					</div>
				</td>
			</tr>
			<tr>
				<td>
				<div id="ACCE3" <?php if($access_type == "Fiber"){ echo "style=\"display:block\""; }else{ echo "style=\"display:none\""; } ?> >
				Provider:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;
				<select id="col2Width" onchange="show(this)" name="fiber_provider">
				<option value="FIBR0"></option>
				<option value="FIBR1" <?php if($_POST['fiber_provider'] == "FIBR1"){ echo "selected"; } ?> >Cbeyond(Dark Fiber)</option>
				<option value="FIBR2" <?php if($_POST['fiber_provider'] == "FIBR2"){ echo "selected"; } ?> >Sidera</option>
				<option value="FIBR3" <?php if($_POST['fiber_provider'] == "FIBR3"){ echo "selected"; } ?> >Time Warner Cable(TWC)</option>
				<option value="FIBR4" <?php if($_POST['fiber_provider'] == "FIBR4"){ echo "selected"; } ?> >TW Telecom(TWTC)</option>
				<option value="FIBR5" <?php if($_POST['fiber_provider'] == "FIBR5"){ echo "selected"; } ?> >Zayo</option>
				<option value="FIBR6" <?php if($_POST['fiber_provider'] == "FIBR6"){ echo "selected"; } ?> >XO(Wholesale EFM)</option>
				</select>
				</div>
				</td>
			</tr>
			<tr>
				<td class="form">BTN:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input id="col2Width" type="text" name="btn" maxlength="10" <?php highlight_input($btn); ?> value="<?=$_POST['btn']?>" /></td>
			</tr>
			</tbody>
			</table>
		</td>

		<td valign="top">
			<table cellspacing="14">
			<tbody>
			<tr>
				<td>
				Voice Service Type:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<select id="col3Width" <?php highlight_select($voice_service_type); ?> onchange="handleVoiceType(this.value);show(this);" name="voice_type">
				<option value="VOIC0" <?php if($voice_service_type == "VOIC0" || $_POST['voice_type'] == "VOIC0"){ echo "selected"; } ?> ></option>
				<option value="VOIC1" <?php if($voice_service_type == "VOIC1" || $_POST['voice_type'] == "VOIC1"){ echo "selected"; } ?> >Analog</option>
				<option value="VOIC2" <?php if($voice_service_type == "VOIC2" || $_POST['voice_type'] == "VOIC2"){ echo "selected"; } ?> >Enhanced Digital</option>
				<option value="VOIC4" <?php if($voice_service_type == "VOIC4" || $_POST['voice_type'] == "VOIC4"){ echo "selected"; } ?> >SIP</option>
				<option value="VOIC5" <?php if($voice_service_type == "VOIC5" || $_POST['voice_type'] == "VOIC5"){ echo "selected"; } ?> >Voice-only PRI</option>
				<option value="VOIC6" <?php if($voice_service_type == "VOIC6" || $_POST['voice_type'] == "VOIC6"){ echo "selected"; } ?> >Mixed</option>
				<option value="VOIC7" <?php if($voice_service_type == "VOIC7" || $_POST['voice_type'] == "VOIC7"){ echo "selected"; } ?> >TCPS (VPS)</option>
				</select>
				</td>
			</tr>
			<tr>
				<td>
					<div id="VOIC1" style="display:none">
					Number of analog lines?
					<select id="col3Width" onchange="multi_show(this)" name="num_analog_lines_ANALOG">
					<option value="ANLG0" >0</option>
					<?php
					for($i=1;$i<=48;$i++){
					echo "<option value=\"ANLG" . $i . "\"";
					if($local_line_count == $i){ echo " selected "; }
					echo ">" . $i . "</option>";
					}
					?>

					</select>
					</div>

					<div id="VOIC2" style="display:none">
						<br>
						Number of trunk group?
						<select id="col3Width" name="num_trunk_groups_PRI">
							<option value="TRGP0"></option>
							<option value="TRGP1" <?php if($_POST['num_trunk_groups_PRI'] == "TRGP1")
								{ echo "selected"; } ?> >1</option>
							<option value="TRGP2" <?php if($_POST['num_trunk_groups_PRI'] == "TRGP2")
								{ echo "selected"; } ?> >2</option>
						</select>
					</div>

					<div id="VOIC3" style="display:none">
						<br>
						Number of CAS trunk groups?
						<select name="num_trunk_groups_CAS">
							<option value="TRGP0"></option>
							<option value="TRGP1" <?php if($_POST['num_trunk_groups_CAS'] == "TRGP1")
								{ echo "selected"; } ?> >1</option>
							<option value="TRGP2" <?php if($_POST['num_trunk_groups_CAS'] == "TRGP2")
								{ echo "selected"; } ?> >2</option>
						</select>
					</div>

					<div id="VOIC4" style="display:none">
						<br>
						Number of analog lines?
						<select id="col3Width" onchange="multi_show(this)" name="num_analog_lines_SIP">
							<option value="ANLG0" <?php if($_POST['num_analog_lines_SIP'] == "ANLG0")
								{ echo "selected"; } ?> >0</option>
							<option value="ANLG1" <?php if($_POST['num_analog_lines_SIP'] == "ANLG1")
								{ echo "selected"; } ?> >1</option>
							<option value="ANLG2" <?php if($_POST['num_analog_lines_SIP'] == "ANLG2")
								{ echo "selected"; } ?> >2</option>
							<option value="ANLG3" <?php if($_POST['num_analog_lines_SIP'] == "ANLG3")
								{ echo "selected"; } ?> >3</option>
							<option value="ANLG4" <?php if($_POST['num_analog_lines_SIP'] == "ANLG4")
								{ echo "selected"; } ?> >4</option>
							<option value="ANLG5" <?php if($_POST['num_analog_lines_SIP'] == "ANLG5")
								{ echo "selected"; } ?> >5</option>
							<option value="ANLG6" <?php if($_POST['num_analog_lines_SIP'] == "ANLG6")
								{ echo "selected"; } ?> >6</option>
							<option value="ANLG7" <?php if($_POST['num_analog_lines_SIP'] == "ANLG7")
								{ echo "selected"; } ?> >7</option>
							<option value="ANLG8" <?php if($_POST['num_analog_lines_SIP'] == "ANLG8")
								{ echo "selected"; } ?> >8</option>
						</select>
					</div>

					<div id="VOIC5" style="display:none">
						<br>
						Number of analog lines?
						<select id="col3Width" onchange="multi_show(this)" name="num_analog_lines_VOPRI">
						<option value="ANLG0" <?php if($_POST['num_analog_lines_VOPRI'] == "ANLG0")
							{ echo "selected"; } ?> >0</option>
						<option value="ANLG1" <?php if($_POST['num_analog_lines_VOPRI'] == "ANLG1")
							{ echo "selected"; } ?> >1</option>
						<option value="ANLG2" <?php if($_POST['num_analog_lines_VOPRI'] == "ANLG2")
							{ echo "selected"; } ?> >2</option>
						<option value="ANLG3" <?php if($_POST['num_analog_lines_VOPRI'] == "ANLG3")
							{ echo "selected"; } ?> >3</option>
						<option value="ANLG4" <?php if($_POST['num_analog_lines_VOPRI'] == "ANLG4")
							{ echo "selected"; } ?> >4</option>
						<option value="ANLG5" <?php if($_POST['num_analog_lines_VOPRI'] == "ANLG5")
							{ echo "selected"; } ?> >5</option>
						<option value="ANLG6" <?php if($_POST['num_analog_lines_VOPRI'] == "ANLG6")
							{ echo "selected"; } ?> >6</option>
						<option value="ANLG7" <?php if($_POST['num_analog_lines_VOPRI'] == "ANLG7")
							{ echo "selected"; } ?> >7</option>
						<option value="ANLG8" <?php if($_POST['num_analog_lines_VOPRI'] == "ANLG8")
							{ echo "selected"; } ?> >8</option>
						</select>
					</div>

					<div id="VOIC6" style="display:none">
						<br>
						Number of PRI trunk groups?
						<select name="num_trunk_groups_PRIMIX">
						<option value="TRGP0"></option>
						<option value="TRGP1" <?php if($_POST['num_trunk_groups_PRIMIX'] == "TRGP1")
							{ echo "selected"; } ?> >1</option>
						<option value="TRGP2" <?php if($_POST['num_trunk_groups_PRIMIX'] == "TRGP2")
							{ echo "selected"; } ?> >2</option>
						</select>
						<br>
						Number of analog lines?
						<select id="col3Width" onchange="multi_show(this)" name="num_analog_lines_PRIMIX">
						<option value="ANLG0" <?php if($_POST['num_analog_lines_PRIMIX'] == "ANLG0")
							{ echo "selected"; } ?> >0</option>
						<option value="ANLG1" <?php if($_POST['num_analog_lines_PRIMIX'] == "ANLG1")
							{ echo "selected"; } ?> >1</option>
						<option value="ANLG2" <?php if($_POST['num_analog_lines_PRIMIX'] == "ANLG2")
							{ echo "selected"; } ?> >2</option>
						<option value="ANLG3" <?php if($_POST['num_analog_lines_PRIMIX'] == "ANLG3")
							{ echo "selected"; } ?> >3</option>
						<option value="ANLG4" <?php if($_POST['num_analog_lines_PRIMIX'] == "ANLG4")
							{ echo "selected"; } ?> >4</option>
						<option value="ANLG5" <?php if($_POST['num_analog_lines_PRIMIX'] == "ANLG5")
							{ echo "selected"; } ?> >5</option>
						<option value="ANLG6" <?php if($_POST['num_analog_lines_PRIMIX'] == "ANLG6")
							{ echo "selected"; } ?> >6</option>
						<option value="ANLG7" <?php if($_POST['num_analog_lines_PRIMIX'] == "ANLG7")
							{ echo "selected"; } ?> >7</option>
						<option value="ANLG8" <?php if($_POST['num_analog_lines_PRIMIX'] == "ANLG8")
							{ echo "selected"; } ?> >8</option>
						</select>
					</div>

					<div id="VOIC7" style="display:none">
						<br>
						Number of PRI trunk groups?
						<select name="num_trunk_groups_TCPS">
							<option value="TRGP0" <?php if($_POST['num_trunk_groups_TCPS'] == "TRGP0")
								{ echo "selected"; } ?> >0</option>
							<option value="TRGP1" <?php if($_POST['num_trunk_groups_TCPS'] == "TRGP1")
								{ echo "selected"; } ?> >1</option>
							<option value="TRGP2" <?php if($_POST['num_trunk_groups_TCPS'] == "TRGP2")
								{ echo "selected"; } ?> >2</option>
						</select>
						<br>
						Number of analog lines?
						<select onchange="multi_show(this)" name="num_analog_lines_TCPS">
							<option value="ANLG0">0</option>
								<?php
									for($i=1;$i<=48;$i++){
										echo "<option value=\"ANLG" . $i . "\"";
									if($local_line_count == $i){ echo " selected "; }
										echo ">" . $i . "</option>";
									}
								?>
						</select>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div id="BWNS" style="display:block;text-align:left;">
					BWNS Profile:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input id="col3Width" type="text" name="bwns_profile" value="<?= $_POST['bwns_profile'] ?>" size="19">
					</div>
				</td>
			</tr>
			<tr>
				<td align="left">
					<div id="CALLC" <?php if($voice_service_type == "VOIC1" || $_POST['voice_type'] == "VOIC1"){ echo "style=\"display:none\""; }else{ echo "style=\"display:block\""; } ?>>
					Call Capacity:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input id="col3Width" type="text" name="call_capacity" value="<?=$_POST['call_capacity']?>" size="19">
					</div>
				</td>
			</tr>
			</tbody>
			</table>
		</td>
	</tr>
	</tbody>
	</table>

	<hr />

	<!--           GENERAL INFO FORM SECTION              -->

	<table width="100%"><tr><td width="75%" style="vertical-align:top;">

	<h4 style="text-decoration:underline;">GENERAL INFORMATION</h4>
	<table style="border-collapse:collapse;">
	<tr><td>Account Number</td>
	<td colspan="2">Account Name</td>
	<td>10K Name</td>
	<td>BWAS</td>
	<td align="center">MPLS?</td>
	</tr>
	<tr>
	<td class="form"><input type="text" name="acct_num" size="14"  <?php highlight_input($acct_num); ?> value="<?=$_POST['acct_num']?>" /></td>
	<td class="form" colspan="2"><input type="text" name="acct_name" size="40" <?php highlight_input($acct_name); ?> value="<?=$_POST['acct_name']?>" /></td>
	<td class="form"><input type="text" name="tenk_name" size="14" <?php highlight_input($tenk_name); ?> value="<?=$_POST['tenk_name']?>" /></td>
	<td class="form"> <input type="text" name="BWAS" size="12"  <?php echo "value=\"".$broadsoft."\"" ?>  readonly/></td>
	<td align="center"><input type="checkbox" name="mpls" /></td>
	
	
	</tr> 

	<tr><td>Primary IAD</td><td>Private IP</td>
	<td>IAD Cidr</td><td>Public IP <span style="font-size:80%">(Network)</span></td><td>Cidr</td></tr>
	<tr><td class="form"><input type="text" name="iad_name" size="14" <?php highlight_input($iadname); ?> value="<?=$_POST['iad_name']?>" /></td>
	<td class="form"><input type="text" name="private_ip" size="14"  value="<?=$_POST['private_ip']?>" /></td>
	<td class="form">  
		<select name="iad_cidr">  
			<option value=""></option>  
			<option value="30" selected >/30</option>  
			<option value="31" <?php if($iad_cidr == 31 || $_POST['iad_cidr'] == "31" ){ echo "selected"; } ?> >/31</option>  
			<option value="29" <?php if($iad_cidr == 29 || $_POST['iad_cidr'] == "29" ){ echo "selected"; } ?> >/29</option>  
		</select>  
	</td>
	<td class="form"><input type="text" name="public_ip" size="14" <?php highlight_input($public_ip); ?> value="<?=$_POST['public_ip']?>" /></td>
	<td class="form"><select name="public_subnet" <?php highlight_select($public_cidr); ?>>
		<option value=""></option>
		<option value="255.255.255.255" <?php if($public_cidr == 32 || $_POST['public_subnet'] == "255.255.255.255"){ echo "selected"; } ?> >/32 (Loopback)</option>
		<option value="255.255.255.252" <?php if($public_cidr == 30 || $_POST['public_subnet'] == "255.255.255.252"){ echo "selected"; } ?> >/30</option>
		<option value="255.255.255.248" <?php if($public_cidr == 29 || $_POST['public_subnet'] == "255.255.255.248"){ echo "selected"; } ?> >/29</option>
		<option value="255.255.255.240" <?php if($public_cidr == 28 || $_POST['public_subnet'] == "255.255.255.240"){ echo "selected"; } ?> >/28</option>
		<option value="255.255.255.224" <?php if($public_cidr == 27 || $_POST['public_subnet'] == "255.255.255.224"){ echo "selected"; } ?> >/27</option>
		<option value="255.255.255.192" <?php if($public_cidr == 26 || $_POST['public_subnet'] == "255.255.255.192"){ echo "selected"; } ?> >/26</option>
		<option value="255.255.255.128" <?php if($public_cidr == 25 || $_POST['public_subnet'] == "255.255.255.128"){ echo "selected"; } ?> >/25</option>
	</select></td>
	</tr>
	</tr>


	<tr><td>

	</table>

	<!--DEVICE DIV -->
	<div id="DEVC1" <?php if($_POST['device'] == "DEVC1" || $device == "IAD"){ echo "style=\"display:block\""; }else{ echo "style=\"display:none\""; } ?> >
	<h5 style="text-decoration:underline;">PRIMARY DEVICE INFORMATION</h5>
	<table><tr><td>IAD MODEL</td><td>SLOT 0</td></tr>
	<tr>
	<td class="form"><select <?php highlight_select(); ?> name="IAD_model">
		<option value=""></option>
		<option value="IAD_8" <?php if($_POST['IAD_model'] == "IAD_8"){ echo "selected"; } ?> >8FXS</option>
		<option value="IAD_16" <?php if($_POST['IAD_model'] == "IAD_16"){ echo "selected"; } ?> >16FXS</option>
		<option value="IAD_24" <?php if($_POST['IAD_model'] == "IAD_24"){ echo "selected"; } ?> >24FXS</option>
		<option value="IAD_T1" <?php if($_POST['IAD_model'] == "IAD_T1"){ echo "selected"; } ?> >T1</option>
	</select></td>
	<td class="form"><select <?php highlight_select(); ?> name="IAD_slot0">
		<option value="">NONE</option>
		<option value="1" <?php if($_POST['IAD_slot0'] == "1"){ echo "selected"; } ?> >Single WIC</option>
		<option value="2" <?php if($_POST['IAD_slot0'] == "2"){ echo "selected"; } ?> >Dual WIC</option>
	</select></td>
	</tr></table>
	</div>

	<div id="DEVC2" <?php if($_POST['device'] == "DEVC2" || $device == "SPIAD"){ echo "style=\"display:block\""; }else{ echo "style=\"display:none\""; } ?> >
	<h5 style="text-decoration:underline;">PRIMARY DEVICE INFORMATION</h5>
	<table><tr><td>SPIAD MODEL</td><td>SLOT 0/0</td><td>SLOT 0/1</td><td>SLOT 0/2</td></tr>
	<tr>
	<td class="form"><select <?php highlight_select(); ?> name="SPIAD_model">
		<option value=""></option>
		<option value="SPIAD_8" <?php if($_POST['SPIAD_model'] == "SPIAD_8"){ echo "selected"; } ?> >8FXS</option>
		<option value="SPIAD_16" <?php if($_POST['SPIAD_model'] == "SPIAD_16"){ echo "selected"; } ?> >16FXS</option>
		<option value="SPIAD_24" <?php if($_POST['SPIAD_model'] == "SPIAD_24"){ echo "selected"; } ?> >24FXS</option>
		<option value="SPIAD_T1" <?php if($_POST['SPIAD_model'] == "SPIAD_T1"){ echo "selected"; } ?> >T1</option>
	</select></td>
	<td class="form"><select <?php highlight_select(); ?> name="SPIAD_slot0">
		<option value="">NONE</option>
		<option value="1" <?php if($_POST['SPIAD_slot0'] == "1"){ echo "selected"; } ?> >Single WIC</option>
		<option value="2" <?php if($_POST['SPIAD_slot0'] == "2"){ echo "selected"; } ?> >Dual WIC</option>
		<option value="4" <?php if($_POST['SPIAD_slot0'] == "4"){ echo "selected"; } ?> >Quad WIC</option>
	</select></td>
	<td class="form"><select <?php highlight_select(); ?> name="SPIAD_slot1">
		<option value="">NONE</option>
		<option value="1" <?php if($_POST['SPIAD_slot1'] == "1"){ echo "selected"; } ?> >Single WIC</option>
		<option value="2" <?php if($_POST['SPIAD_slot1'] == "2"){ echo "selected"; } ?> >Dual WIC</option>
		<option value="4" <?php if($_POST['SPIAD_slot1'] == "4"){ echo "selected"; } ?> >Quad WIC</option>
	</select></td>
	<td class="form"><select <?php highlight_select(); ?> name="SPIAD_slot2">
		<option value="">NONE</option>
		<option value="1" <?php if($_POST['SPIAD_slot2'] == "1"){ echo "selected"; } ?> >Single WIC</option>
		<option value="2" <?php if($_POST['SPIAD_slot2'] == "2"){ echo "selected"; } ?> >Dual WIC</option>
		<option value="4" <?php if($_POST['SPIAD_slot2'] == "4"){ echo "selected"; } ?> >Quad WIC</option>
	</select></td>
	</tr></table>
	</div>


	<div id="DEVC3" <?php if($_POST['device'] == "DEVC3" || $device == "ISR"){ echo "style=\"display:block\""; }else{ echo "style=\"display:none\""; } ?> >
	<h5 style="text-decoration:underline;">PRIMARY DEVICE INFORMATION</h5>
	<table><tr><td>SLOT 0/0</td><td>SLOT 0/1</td><td>SLOT 0/2</td><td>SLOT 0/3</td></tr>
	<tr>

	<td class="form"><input type="hidden" name="ISR_model" value="ISR_24">
	<select <?php highlight_select(); ?> name="ISR_slot0">
		<option value="">NONE</option>
		<option value="1" <?php if($_POST['ISR_slot0'] == "1"){ echo "selected"; } ?> >Single WIC</option>
		<option value="2" <?php if($_POST['ISR_slot0'] == "2"){ echo "selected"; } ?> >Dual WIC</option>
		<option value="4" <?php if($_POST['ISR_slot0'] == "4"){ echo "selected"; } ?> >Quad WIC</option>
	</select></td>
	<td class="form"><select <?php highlight_select(); ?> name="ISR_slot1">
		<option value="">NONE</option>
		<option value="1" <?php if($_POST['ISR_slot1'] == "1"){ echo "selected"; } ?> >Single WIC</option>
		<option value="2" <?php if($_POST['ISR_slot1'] == "2"){ echo "selected"; } ?> >Dual WIC</option>
		<option value="4" <?php if($_POST['ISR_slot1'] == "4"){ echo "selected"; } ?> >Quad WIC</option>
	</select></td>
	<td class="form"><select <?php highlight_select(); ?> name="ISR_slot2">
		<option value="">NONE</option>
		<option value="1" <?php if($_POST['ISR_slot2'] == "1"){ echo "selected"; } ?> >Single WIC</option>
		<option value="2" <?php if($_POST['ISR_slot2'] == "2"){ echo "selected"; } ?> >Dual WIC</option>
		<option value="4" <?php if($_POST['ISR_slot2'] == "4"){ echo "selected"; } ?> >Quad WIC</option>
	</select></td>
	<td class="form"><select <?php highlight_select(); ?> name="ISR_slot3">
		<option value="">NONE</option>
		<option value="1" <?php if($_POST['ISR_slot3'] == "1"){ echo "selected"; } ?> >Single WIC</option>
		<option value="2" <?php if($_POST['ISR_slot3'] == "2"){ echo "selected"; } ?> >Dual WIC</option>
		<option value="4" <?php if($_POST['ISR_slot3'] == "4"){ echo "selected"; } ?> >Quad WIC</option>
	</select></td>
	</tr></table>
	</div>


	<!--SECONDARY DEVICE DIV -->
	<div id="SCDV1" <?php if($_POST['second_device'] == "SCDV1"){ echo "style=\"display:block\""; }else{ echo "style=\"display:none\""; } ?> >
	<h5 style="text-decoration:underline;">SECONDARY DEVICE INFORMATION</h5>
	<table><tr><td>Secondary IAD Name</td><td>Secondary Private IP</td><td>IAD Cidr</td><td>IAD MODEL</td><td>SLOT 0</td></tr>
	<tr>
	<td class="form"><input type="text" name="IAD_name_secondary"  value="<?= $_POST['IAD_name_secondary'] ?>"size="14" /></td>
	<td class="form"><input type="text" name="IAD_secondary_private_ip" size="14"  value="<?=$_POST['IAD_secondary_private_ip']?>" /></td>
	<td class="form">  
        <select name="IAD_cidr_secondary">  
            <option value=""></option>
			<option value="30" selected >/30</option> 
            <option value="31" <?php if($_POST['IAD_cidr_secondary'] == "31"){ echo "selected"; } ?> >/31</option>  
            <option value="29" <?php if($_POST['IAD_cidr_secondary'] == "29"){ echo "selected"; } ?> >/29</option>  
        </select>   
	</td>
	<td class="form"><select <?php highlight_select(); ?> name="IAD_model_secondary">
		<option value=""></option>
		<option value="IAD_8" <?php if($_POST['IAD_model_secondary'] == "IAD_8"){ echo "selected"; } ?> >8FXS</option>
		<option value="IAD_16" <?php if($_POST['IAD_model_secondary'] == "IAD_16"){ echo "selected"; } ?> >16FXS</option>
		<option value="IAD_24" <?php if($_POST['IAD_model_secondary'] == "IAD_24"){ echo "selected"; } ?> >24FXS</option>
		<option value="IAD_T1" <?php if($_POST['IAD_model_secondary'] == "IAD_T1"){ echo "selected"; } ?> >T1</option>
	</select></td>
	<td class="form"><select <?php highlight_select(); ?> name="IAD_slot0_secondary">
		<option value="">NONE</option>
		<option value="1" <?php if($_POST['IAD_slot0_secondary'] == "1"){ echo "selected"; } ?> >Single WIC</option>
		<option value="2" <?php if($_POST['IAD_slot0_secondary'] == "2"){ echo "selected"; } ?> >Dual WIC</option>
	</select></td>
	</tr></table>
	</div>

	<div id="SCDV2" <?php if($_POST['second_device'] == "SCDV2"){ echo "style=\"display:block\""; }else{ echo "style=\"display:none\""; } ?> >
	<h5 style="text-decoration:underline;">SECONDARY DEVICE INFORMATION</h5>
	<table><tr><td>Secondary IAD Name</td><td>Secondary Private IP</td><td>IAD Cidr</td><td>SPIAD MODEL</td><td>SLOT 0/0</td><td>SLOT 0/1</td><td>SLOT 0/2</td></tr>
	<tr>
	<td class="form"><input type="text" name="SPIAD_name_secondary" value="<?= $_POST['SPIAD_name_secondary'] ?>" size="14" />
	</td>
	<td class="form"><input type="text" name="SPIAD_secondary_private_ip" size="14"  value="<?= $_POST['SPIAD_secondary_private_ip'] ?>" /></td>
	<td class="form">  
        <select name="SPIAD_cidr_secondary">  
            <option value=""></option>  
            <option value="30" selected >/30</option>
			<option value="31" <?php if($_POST['SPIAD_cidr_secondary'] == "31"){ echo "selected"; } ?> >/31</option>  
            <option value="29" <?php if($_POST['SPIAD_cidr_secondary'] == "29"){ echo "selected"; } ?> >/29</option>  
        </select>  
    </td>
	<td class="form"><select <?php highlight_select(); ?> name="SPIAD_model_secondary">
		<option value=""></option>
		<option value="SPIAD_8" <?php if($_POST['SPIAD_model_secondary'] == "SPIAD_8"){ echo "selected"; } ?> >8FXS</option>
		<option value="SPIAD_16" <?php if($_POST['SPIAD_model_secondary'] == "SPIAD_16"){ echo "selected"; } ?> >16FXS</option>
		<option value="SPIAD_24" <?php if($_POST['SPIAD_model_secondary'] == "SPIAD_24"){ echo "selected"; } ?> >24FXS</option>
		<option value="SPIAD_T1" <?php if($_POST['SPIAD_model_secondary'] == "SPIAD_T1"){ echo "selected"; } ?> >T1</option>
	</select></td>
	<td class="form"><select <?php highlight_select(); ?> name="SPIAD_slot0_secondary">
		<option value="">NONE</option>
		<option value="1" <?php if($_POST['SPIAD_slot0_secondary'] == "1"){ echo "selected"; } ?> >Single WIC</option>
		<option value="2" <?php if($_POST['SPIAD_slot0_secondary'] == "2"){ echo "selected"; } ?> >Dual WIC</option>
		<option value="4" <?php if($_POST['SPIAD_slot0_secondary'] == "4"){ echo "selected"; } ?> >Quad WIC</option>
	</select></td>
	<td class="form"><select <?php highlight_select(); ?> name="SPIAD_slot1_secondary">
		<option value="">NONE</option>
		<option value="1" <?php if($_POST['SPIAD_slot1_secondary'] == "1"){ echo "selected"; } ?> >Single WIC</option>
		<option value="2" <?php if($_POST['SPIAD_slot1_secondary'] == "2"){ echo "selected"; } ?> >Dual WIC</option>
		<option value="4" <?php if($_POST['SPIAD_slot1_secondary'] == "4"){ echo "selected"; } ?> >Quad WIC</option>
	</select></td>
	<td class="form"><select <?php highlight_select(); ?> name="SPIAD_slot2_secondary">
		<option value="">NONE</option>
		<option value="1" <?php if($_POST['SPIAD_slot2_secondary'] == "1"){ echo "selected"; } ?> >Single WIC</option>
		<option value="2" <?php if($_POST['SPIAD_slot2_secondary'] == "2"){ echo "selected"; } ?> >Dual WIC</option>
		<option value="4" <?php if($_POST['SPIAD_slot2_secondary'] == "4"){ echo "selected"; } ?> >Quad WIC</option>
	</select></td>
	</tr></table>
	</div>


	<div id="SCDV3" <?php if($_POST['second_device'] == "SCDV3"){ echo "style=\"display:block\""; }else{ echo "style=\"display:none\""; } ?> >
	<h5 style="text-decoration:underline;">SECONDARY DEVICE INFORMATION</h5>
	<table><tr><td>Secondary IAD Name</td><td>Secondary Private IP</td><td>IAD Cidr</td><td>SLOT 0/0</td><td>SLOT 0/1</td><td>SLOT 0/2</td><td>SLOT 0/3</td></tr>
	<tr>
	<td class="form"><input type="text" name="ISR_name_secondary" value="<?= $_POST['ISR_name_secondary'] ?>" size="14" />
	</td>
	<td class="form"><input type="text" name="ISR_secondary_private_ip" size="14"  value="<?= $_POST['ISR_secondary_private_ip'] ?>" /></td>
	<td class="form">  
        <select name="ISR_cidr_secondary">  
            <option value=""></option>  
			<option value="30" selected >/30</option>
            <option value="31" <?php if($_POST['ISR_cidr_secondary'] == "31"){ echo "selected"; } ?> >/31</option>  
            <option value="29" <?php if($_POST['ISR_cidr_secondary'] == "29"){ echo "selected"; } ?> >/29</option>  
        </select>  
    </td>
	<td class="form"><input type="hidden" name="ISR_model_secondary" value="ISR_24_secondary">
	<select <?php highlight_select(); ?> name="ISR_slot0_secondary">
		<option value="">NONE</option>
		<option value="1" <?php if($_POST['ISR_slot0_secondary'] == "1"){ echo "selected"; } ?> >Single WIC</option>
		<option value="2" <?php if($_POST['ISR_slot0_secondary'] == "2"){ echo "selected"; } ?> >Dual WIC</option>
		<option value="4" <?php if($_POST['ISR_slot0_secondary'] == "4"){ echo "selected"; } ?> >Quad WIC</option>
	</select></td>
	<td class="form"><select <?php highlight_select(); ?> name="ISR_slot1_secondary">
		<option value="">NONE</option>
		<option value="1" <?php if($_POST['ISR_slot1_secondary'] == "1"){ echo "selected"; } ?> >Single WIC</option>
		<option value="2" <?php if($_POST['ISR_slot1_secondary'] == "2"){ echo "selected"; } ?> >Dual WIC</option>
		<option value="4" <?php if($_POST['ISR_slot1_secondary'] == "4"){ echo "selected"; } ?> >Quad WIC</option>
	</select></td>
	<td class="form"><select <?php highlight_select(); ?> name="ISR_slot2_secondary">
		<option value="">NONE</option>
		<option value="1" <?php if($_POST['ISR_slot2_secondary'] == "1"){ echo "selected"; } ?> >Single WIC</option>
		<option value="2" <?php if($_POST['ISR_slot2_secondary'] == "2"){ echo "selected"; } ?> >Dual WIC</option>
		<option value="4" <?php if($_POST['ISR_slot2_secondary'] == "4"){ echo "selected"; } ?> >Quad WIC</option>
	</select></td>
	<td class="form"><select <?php highlight_select(); ?> name="ISR_slot3_secondary">
		<option value="">NONE</option>
		<option value="1" <?php if($_POST['ISR_slot3_secondary'] == "1"){ echo "selected"; } ?> >Single WIC</option>
		<option value="2" <?php if($_POST['ISR_slot3_secondary'] == "2"){ echo "selected"; } ?> >Dual WIC</option>
		<option value="4" <?php if($_POST['ISR_slot3_secondary'] == "4"){ echo "selected"; } ?> >Quad WIC</option>
	</select></td>
	</tr></table>
	</div>




	<!--FIBER CIRCUIT DIV -->
	<div id="FIBR1" <?php if($_POST['fiber_provider'] == "FIBR1"){ echo "style=\"display:block\""; }else{ echo "style=\"display:none\""; } ?> >
	<h5 style="text-decoration:underline;">CBEYOND DARK FIBER CIRCUIT INFORMATION</h5>
	<table><tr><td>Fiber Circuit ID</td><td>Site VLAN ID</td><td>Customer VLAN ID</td><td>Committed Bandwidth</td><td>Secondary 10K Name</td></tr>
	<tr><td class="form"><input type="text" name="CID_FIBR1" <?php if($access_type == "Fiber"){ highlight_input($circ_cid[1]); }?> value="<?=$_POST['CID_FIBR1']?>" /></td>
	<td class="form"><input type="text" name="SITE_VLAN_FIBR1" <?php if($access_type == "Fiber"){ highlight_input($site_vlan); }?> value="<?=$_POST['SITE_VLAN_FIBR1']?>" /></td>
	<td class="form"><input type="text" name="VLAN_FIBR1" <?php if($access_type == "Fiber"){ highlight_input($vlan); }?> value="<?=$_POST['VLAN_FIBR1']?>" /></td>
	<td class="form"><select <?php highlight_select($committed_bw); ?> name="rate_limit_FIBR1">
		<option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
		<option value="2" <?php if($committed_bw == 2 || $_POST['rate_limit_FIBR1'] == "2"){ echo "selected"; } ?> >2</option>
		<option value="4" <?php if($committed_bw == 4 || $_POST['rate_limit_FIBR1'] == "4"){ echo "selected"; } ?> >4</option>
		<option value="6" <?php if($committed_bw == 6 || $_POST['rate_limit_FIBR1'] == "6"){ echo "selected"; } ?> >6</option>
		<option value="8" <?php if($committed_bw == 8 || $_POST['rate_limit_FIBR1'] == "8"){ echo "selected"; } ?> >8</option>
		<option value="10" <?php if($committed_bw == 10 || $_POST['rate_limit_FIBR1'] == "10"){ echo "selected"; } ?> >10</option>
		<option value="15" <?php if($committed_bw == 15 || $_POST['rate_limit_FIBR1'] == "15"){ echo "selected"; } ?> >15</option>
		<option value="20" <?php if($committed_bw == 20 || $_POST['rate_limit_FIBR1'] == "20"){ echo "selected"; } ?> >20</option>
		<option value="25" <?php if($committed_bw == 25 || $_POST['rate_limit_FIBR1'] == "25"){ echo "selected"; } ?> >25</option>
		<option value="30" <?php if($committed_bw == 30 || $_POST['rate_limit_FIBR1'] == "30"){ echo "selected"; } ?> >30</option>
		<option value="35" <?php if($committed_bw == 35 || $_POST['rate_limit_FIBR1'] == "35"){ echo "selected"; } ?> >35</option>
		<option value="40" <?php if($committed_bw == 40 || $_POST['rate_limit_FIBR1'] == "40"){ echo "selected"; } ?> >40</option>
		<option value="45" <?php if($committed_bw == 45 || $_POST['rate_limit_FIBR1'] == "45"){ echo "selected"; } ?> >45</option>
		<option value="50" <?php if($committed_bw == 50 || $_POST['rate_limit_FIBR1'] == "50"){ echo "selected"; } ?> >50</option>
		<option value="100" <?php if($committed_bw == 100 || $_POST['rate_limit_FIBR1'] == "100"){ echo "selected"; } ?> >100</option>
	</select></td>
	<td class="form"><input type="text" name="secondary_10k_name_FIBR1" <?php if($access_type == "Fiber"){ highlight_input($secondary_tenk_name); }?> value="<?=$_POST['secondary_10k_name_FIBR1']?>" /></td></tr>
	<tr><td>BAS Port (ex. "1/1/2")</td></tr><tr><td class="form"><input type="text" name="BAS_PORT" <?php if($access_type == "Fiber"){ highlight_input(); }?> value="<?=$_POST['BAS_PORT']?>"/></td></tr>
	</table>
	</select>
	</div>

	<div id="FIBR2" <?php if($_POST['fiber_provider'] == "FIBR2"){ echo "style=\"display:block\""; }else{ echo "style=\"display:none\""; } ?> >
	<h5 style="text-decoration:underline;">SIDERA FIBER CIRCUIT INFORMATION</h5>
	<table><tr><td>Fiber Circuit ID</td><td>10K Interface</td><td>VLAN ID</td><td>Committed Bandwidth</td></tr>
	<tr><td class="form"><input type="text" name="CID_FIBR2" <?php if($access_type == "Fiber"){ highlight_input($circ_cid[1]); }?> value="<?=$_POST['CID_FIBR2']?>"/></td>
	<td class="form"><input type="text" name="TENK_INTERFACE_FIBR2" <?php if($access_type == "Fiber"){ highlight_input($tenk_interface); }?> value="<?=$_POST['TENK_INTERFACE_FIBR2']?>"/></td>
	<td class="form"><input type="text" name="VLAN_FIBR2" <?php if($access_type == "Fiber"){ highlight_input($vlan); }?> value="<?=$_POST['VLAN_FIBR2']?>"/></td>
	<td class="form"><select <?php highlight_select($committed_bw); ?> name="rate_limit_FIBR2">
		<option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
		<option value="2" <?php if($committed_bw == 2 || $_POST['rate_limit_FIBR2'] == "2"){ echo "selected"; } ?> >2</option>
		<option value="4" <?php if($committed_bw == 4 || $_POST['rate_limit_FIBR2'] == "4"){ echo "selected"; } ?> >4</option>
		<option value="6" <?php if($committed_bw == 6 || $_POST['rate_limit_FIBR2'] == "6"){ echo "selected"; } ?> >6</option>
		<option value="8" <?php if($committed_bw == 8 || $_POST['rate_limit_FIBR2'] == "8"){ echo "selected"; } ?> >8</option>
		<option value="10" <?php if($committed_bw == 10 || $_POST['rate_limit_FIBR2'] == "10"){ echo "selected"; } ?> >10</option>
		<option value="15" <?php if($committed_bw == 15 || $_POST['rate_limit_FIBR2'] == "15"){ echo "selected"; } ?> >15</option>
		<option value="20" <?php if($committed_bw == 20 || $_POST['rate_limit_FIBR2'] == "20"){ echo "selected"; } ?> >20</option>
		<option value="25" <?php if($committed_bw == 25 || $_POST['rate_limit_FIBR2'] == "25"){ echo "selected"; } ?> >25</option>
		<option value="30" <?php if($committed_bw == 30 || $_POST['rate_limit_FIBR2'] == "30"){ echo "selected"; } ?> >30</option>
		<option value="35" <?php if($committed_bw == 35 || $_POST['rate_limit_FIBR2'] == "35"){ echo "selected"; } ?> >35</option>
		<option value="40" <?php if($committed_bw == 40 || $_POST['rate_limit_FIBR2'] == "40"){ echo "selected"; } ?> >40</option>
		<option value="45" <?php if($committed_bw == 45 || $_POST['rate_limit_FIBR2'] == "45"){ echo "selected"; } ?> >45</option>
		<option value="50" <?php if($committed_bw == 50 || $_POST['rate_limit_FIBR2'] == "50"){ echo "selected"; } ?> >50</option>
		<option value="100" <?php if($committed_bw == 100 || $_POST['rate_limit_FIBR2'] == "100"){ echo "selected"; } ?> >100</option>
	</select></td>
	</tr></table>
	</div>

	<div id="FIBR3" <?php if($_POST['fiber_provider'] == "FIBR3"){ echo "style=\"display:block\""; }else{ echo "style=\"display:none\""; } ?> >
	<h5 style="text-decoration:underline;">TWC FIBER CIRCUIT INFORMATION</h5>
	<table><tr><td>Fiber Circuit ID</td><td>10K Interface</td><td>VLAN ID</td><td>Committed Bandwidth</td></tr>
	<tr><td class="form"><input type="text" name="CID_FIBR3" <?php if($access_type == "Fiber"){ highlight_input($circ_cid[1]); }?> value="<?=$_POST['CID_FIBR3']?>"/></td>
	<td class="form"><input type="text" name="TENK_INTERFACE_FIBR3" <?php if($access_type == "Fiber"){ highlight_input($tenk_interface); }?> value="<?=$_POST['TENK_INTERFACE_FIBR3']?>"/></td>
	<td class="form"><input type="text" name="VLAN_FIBR3" <?php if($access_type == "Fiber"){ highlight_input($vlan); }?> value="<?=$_POST['VLAN_FIBR3']?>" /></td>
	<td class="form"><select <?php highlight_select($committed_bw); ?> name="rate_limit_FIBR3">
		<option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
		<option value="2" <?php if($committed_bw == 2 || $_POST['rate_limit_FIBR3'] == "2"){ echo "selected"; } ?> >2</option>
		<option value="4" <?php if($committed_bw == 4 || $_POST['rate_limit_FIBR3'] == "4"){ echo "selected"; } ?> >4</option>
		<option value="6" <?php if($committed_bw == 6 || $_POST['rate_limit_FIBR3'] == "6"){ echo "selected"; } ?> >6</option>
		<option value="8" <?php if($committed_bw == 8 || $_POST['rate_limit_FIBR3'] == "8"){ echo "selected"; } ?> >8</option>
		<option value="10" <?php if($committed_bw == 10 || $_POST['rate_limit_FIBR3'] == "10"){ echo "selected"; } ?> >10</option>
		<option value="15" <?php if($committed_bw == 15 || $_POST['rate_limit_FIBR3'] == "15"){ echo "selected"; } ?> >15</option>
		<option value="20" <?php if($committed_bw == 20 || $_POST['rate_limit_FIBR3'] == "20"){ echo "selected"; } ?> >20</option>
		<option value="25" <?php if($committed_bw == 25 || $_POST['rate_limit_FIBR3'] == "25"){ echo "selected"; } ?> >25</option>
		<option value="30" <?php if($committed_bw == 30 || $_POST['rate_limit_FIBR3'] == "30"){ echo "selected"; } ?> >30</option>
		<option value="35" <?php if($committed_bw == 35 || $_POST['rate_limit_FIBR3'] == "35"){ echo "selected"; } ?> >35</option>
		<option value="40" <?php if($committed_bw == 40 || $_POST['rate_limit_FIBR3'] == "40"){ echo "selected"; } ?> >40</option>
		<option value="45" <?php if($committed_bw == 45 || $_POST['rate_limit_FIBR3'] == "45"){ echo "selected"; } ?> >45</option>
		<option value="50" <?php if($committed_bw == 50 || $_POST['rate_limit_FIBR3'] == "50"){ echo "selected"; } ?> >50</option>
		<option value="100" <?php if($committed_bw == 100 || $_POST['rate_limit_FIBR3'] == "100"){ echo "selected"; } ?> >100</option>
	</select></td>
	</tr></table>
	</div>

	<div id="FIBR4" <?php if($_POST['fiber_provider'] == "FIBR4"){ echo "style=\"display:block\""; }else{ echo "style=\"display:none\""; } ?> >
	<h5 style="text-decoration:underline;">TWTC FIBER CIRCUIT INFORMATION</h5>
	<table><tr><td>Fiber Circuit ID</td><td>10K Interface</td><td>VLAN ID</td><td>Committed Bandwidth</td></tr>
	<tr><td class="form"><input type="text" name="CID_FIBR4" <?php if($access_type == "Fiber"){ highlight_input($circ_cid[1]); }?> value="<?=$_POST['CID_FIBR4']?>"/></td>
	<td class="form"><input type="text" name="TENK_INTERFACE_FIBR4" <?php if($access_type == "Fiber"){ highlight_input($tenk_interface); }?> value="<?=$_POST['TENK_INTERFACE_FIBR4']?>"/></td>
	<td class="form"><input type="text" name="VLAN_FIBR4" <?php if($access_type == "Fiber"){ highlight_input($vlan); }?> value="<?=$_POST['VLAN_FIBR4']?>"/></td>
	<td class="form"><select <?php highlight_select($committed_bw); ?> name="rate_limit_FIBR4">
		<option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
		<option value="2" <?php if($committed_bw == 2 || $_POST['rate_limit_FIBR4'] == "2"){ echo "selected"; } ?> >2</option>
		<option value="4" <?php if($committed_bw == 4 || $_POST['rate_limit_FIBR4'] == "4"){ echo "selected"; } ?> >4</option>
		<option value="6" <?php if($committed_bw == 6 || $_POST['rate_limit_FIBR4'] == "6"){ echo "selected"; } ?> >6</option>
		<option value="8" <?php if($committed_bw == 8 || $_POST['rate_limit_FIBR4'] == "8"){ echo "selected"; } ?> >8</option>
		<option value="10" <?php if($committed_bw == 10 || $_POST['rate_limit_FIBR4'] == "10"){ echo "selected"; } ?> >10</option>
		<option value="15" <?php if($committed_bw == 15 || $_POST['rate_limit_FIBR4'] == "15"){ echo "selected"; } ?> >15</option>
		<option value="20" <?php if($committed_bw == 20 || $_POST['rate_limit_FIBR4'] == "20"){ echo "selected"; } ?> >20</option>
		<option value="25" <?php if($committed_bw == 25 || $_POST['rate_limit_FIBR4'] == "25"){ echo "selected"; } ?> >25</option>
		<option value="30" <?php if($committed_bw == 30 || $_POST['rate_limit_FIBR4'] == "30"){ echo "selected"; } ?> >30</option>
		<option value="35" <?php if($committed_bw == 35 || $_POST['rate_limit_FIBR4'] == "35"){ echo "selected"; } ?> >35</option>
		<option value="40" <?php if($committed_bw == 40 || $_POST['rate_limit_FIBR4'] == "40"){ echo "selected"; } ?> >40</option>
		<option value="45" <?php if($committed_bw == 45 || $_POST['rate_limit_FIBR4'] == "45"){ echo "selected"; } ?> >45</option>
		<option value="50" <?php if($committed_bw == 50 || $_POST['rate_limit_FIBR4'] == "50"){ echo "selected"; } ?> >50</option>
		<option value="100" <?php if($committed_bw == 100 || $_POST['rate_limit_FIBR4'] == "100"){ echo "selected"; } ?> >100</option>
	</select></td>
	</tr></table>
	</div>

	<div id="FIBR5" <?php if($_POST['fiber_provider'] == "FIBR5"){ echo "style=\"display:block\""; }else{ echo "style=\"display:none\""; } ?> >
	<h5 style="text-decoration:underline;">ZAYO FIBER CIRCUIT INFORMATION</h5>
	<table><tr><td>Fiber Circuit ID</td><td>10K Interface</td><td>VLAN ID</td><td>Committed Bandwidth</td></tr>
	<tr><td class="form"><input type="text" name="CID_FIBR5" <?php if($access_type == "Fiber"){ highlight_input($circ_cid[1]); }?> value="<?=$_POST['CID_FIBR5']?>"/></td>
	<td class="form"><input type="text" name="TENK_INTERFACE_FIBR5" <?php if($access_type == "Fiber"){ highlight_input($tenk_interface); }?> value="<?=$_POST['TENK_INTERFACE_FIBR5']?>"/></td>
	<td class="form"><input type="text" name="VLAN_FIBR5" <?php if($access_type == "Fiber"){ highlight_input($vlan); }?> value="<?=$_POST['VLAN_FIBR5']?>"/></td>
	<td class="form"><select <?php highlight_select($committed_bw); ?> name="rate_limit_FIBR5">
		<option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
		<option value="2" <?php if($committed_bw == 2 || $_POST['rate_limit_FIBR5'] == "2"){ echo "selected"; } ?> >2</option>
		<option value="4" <?php if($committed_bw == 4 || $_POST['rate_limit_FIBR5'] == "4"){ echo "selected"; } ?> >4</option>
		<option value="6" <?php if($committed_bw == 6 || $_POST['rate_limit_FIBR5'] == "6"){ echo "selected"; } ?> >6</option>
		<option value="8" <?php if($committed_bw == 8 || $_POST['rate_limit_FIBR5'] == "8"){ echo "selected"; } ?> >8</option>
		<option value="10" <?php if($committed_bw == 10 || $_POST['rate_limit_FIBR5'] == "10"){ echo "selected"; } ?> >10</option>
		<option value="15" <?php if($committed_bw == 15 || $_POST['rate_limit_FIBR5'] == "15"){ echo "selected"; } ?> >15</option>
		<option value="20" <?php if($committed_bw == 20 || $_POST['rate_limit_FIBR5'] == "20"){ echo "selected"; } ?> >20</option>
		<option value="25" <?php if($committed_bw == 25 || $_POST['rate_limit_FIBR5'] == "25"){ echo "selected"; } ?> >25</option>
		<option value="30" <?php if($committed_bw == 30 || $_POST['rate_limit_FIBR5'] == "30"){ echo "selected"; } ?> >30</option>
		<option value="35" <?php if($committed_bw == 35 || $_POST['rate_limit_FIBR5'] == "35"){ echo "selected"; } ?> >35</option>
		<option value="40" <?php if($committed_bw == 40 || $_POST['rate_limit_FIBR5'] == "40"){ echo "selected"; } ?> >40</option>
		<option value="45" <?php if($committed_bw == 45 || $_POST['rate_limit_FIBR5'] == "45"){ echo "selected"; } ?> >45</option>
		<option value="50" <?php if($committed_bw == 50 || $_POST['rate_limit_FIBR5'] == "50"){ echo "selected"; } ?> >50</option>
		<option value="100" <?php if($committed_bw == 100 || $_POST['rate_limit_FIBR5'] == "100"){ echo "selected"; } ?> >100</option>
	</select></td>
	</tr></table>
	</div>

	<div id="FIBR6" <?php if($_POST['fiber_provider'] == "FIBR6"){ echo "style=\"display:block\""; }else{ echo "style=\"display:none\""; } ?> >
	<h5 style="text-decoration:underline;">XO WHOLESALE EFM CIRCUIT INFORMATION</h5>
	<table><tr><td>Circuit ID</td><td>10K Interface</td><td>VLAN ID</td><td>Committed Bandwidth</td></tr>
	<tr><td class="form"><input type="text" name="CID_FIBR6" <?php if($access_type == "Fiber"){ highlight_input($circ_cid[1]); }?> value="<?=$_POST['CID_FIBR6']?>"/></td>
	<td class="form"><input type="text" name="TENK_INTERFACE_FIBR6" <?php if($access_type == "Fiber"){ highlight_input($tenk_interface); }?> value="<?=$_POST['TENK_INTERFACE_FIBR6']?>"/></td>
	<td class="form"><input type="text" name="VLAN_FIBR6" <?php if($access_type == "Fiber"){ highlight_input($vlan); }?> value="<?=$_POST['VLAN_FIBR6']?>"/></td>
	<td class="form"><select <?php highlight_select($committed_bw); ?> name="rate_limit_FIBR6">
		<option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
		<option value="2" <?php if($committed_bw == 2 || $_POST['rate_limit_FIBR6'] == "2"){ echo "selected"; } ?> >2</option>
		<option value="4" <?php if($committed_bw == 4 || $_POST['rate_limit_FIBR6'] == "4"){ echo "selected"; } ?> >4</option>
		<option value="6" <?php if($committed_bw == 6 || $_POST['rate_limit_FIBR6'] == "6"){ echo "selected"; } ?> >6</option>
		<option value="8" <?php if($committed_bw == 8 || $_POST['rate_limit_FIBR6'] == "8"){ echo "selected"; } ?> >8</option>
		<option value="10" <?php if($committed_bw == 10 || $_POST['rate_limit_FIBR6'] == "10"){ echo "selected"; } ?> >10</option>
		<option value="15" <?php if($committed_bw == 15 || $_POST['rate_limit_FIBR6'] == "15"){ echo "selected"; } ?> >15</option>
		<option value="20" <?php if($committed_bw == 20 || $_POST['rate_limit_FIBR6'] == "20"){ echo "selected"; } ?> >20</option>
		<option value="25" <?php if($committed_bw == 25 || $_POST['rate_limit_FIBR6'] == "25"){ echo "selected"; } ?> >25</option>
		<option value="30" <?php if($committed_bw == 30 || $_POST['rate_limit_FIBR6'] == "30"){ echo "selected"; } ?> >30</option>
		<option value="35" <?php if($committed_bw == 35 || $_POST['rate_limit_FIBR6'] == "35"){ echo "selected"; } ?> >35</option>
		<option value="40" <?php if($committed_bw == 40 || $_POST['rate_limit_FIBR6'] == "40"){ echo "selected"; } ?> >40</option>
		<option value="45" <?php if($committed_bw == 45 || $_POST['rate_limit_FIBR6'] == "45"){ echo "selected"; } ?> >45</option>
		<option value="50" <?php if($committed_bw == 50 || $_POST['rate_limit_FIBR6'] == "50"){ echo "selected"; } ?> >50</option>
		<option value="100" <?php if($committed_bw == 100 || $_POST['rate_limit_FIBR6'] == "100"){ echo "selected"; } ?> >100</option>
	</select></td>
	</tr></table>
	</div>



	<!--T-1 CIRCUIT ID DIVs -->
	<div id="TONE1" <?php if($access_type == "T1" && $circ_count >= 1){ echo "style=\"display:block\""; }else{ echo "style=\"display:none\""; } ?> >
	<h5 style="text-decoration:underline;">T1 CIRCUIT INFORMATION</h5>
	<table style="border-collapse:collapse;">
	<tr><td>Serial Interface 1: <input type="text" name="SERIAL1" value="<?php if($access_type == "T1"){ echo $serial[1]; }?>"/></td><td>T1 Circuit ID 1: <input type="text" name="CID1_T1" value="<?php if($access_type == "T1"){ echo $circ_cid[1]; }?>"/></td></tr>
	</table>
	</div>
	<div id="TONE2" <?php if($access_type == "T1" && $circ_count >= 2){ echo "style=\"display:block\""; }else{ echo "style=\"display:none\""; } ?> >
	<table style="border-collapse:collapse;">
	<tr><td>Serial Interface 2: <input type="text" name="SERIAL2" value="<?php if($access_type == "T1"){ echo $serial[2]; }?>"/></td><td>T1 Circuit ID 2: <input type="text" name="CID2_T1" value="<?php if($access_type == "T1"){ echo $circ_cid[2]; }?>"/></td></tr>
	</table>
	</div>
	<div id="TONE3" <?php if($access_type == "T1" && $circ_count >= 3){ echo "style=\"display:block\""; }else{ echo "style=\"display:none\""; } ?> >
	<table style="border-collapse:collapse;">
	<tr><td>Serial Interface 3: <input type="text" name="SERIAL3" value="<?php if($access_type == "T1"){ echo $serial[3]; }?>"/></td><td>T1 Circuit ID 3: <input type="text" name="CID3_T1" value="<?php if($access_type == "T1"){ echo $circ_cid[3]; }?>"/></td></tr>
	</table>
	</div>
	<div id="TONE4" <?php if($access_type == "T1" && $circ_count >= 4){ echo "style=\"display:block\""; }else{ echo "style=\"display:none\""; } ?> >
	<table style="border-collapse:collapse;">
	<tr><td>Serial Interface 4: <input type="text" name="SERIAL4" value="<?php if($access_type == "T1"){ echo $serial[4]; }?>"/></td><td>T1 Circuit ID 4: <input type="text" name="CID4_T1" value="<?php if($access_type == "T1"){ echo $circ_cid[4]; }?>"/></td></tr>
	</table>
	</div>
	<div id="TONE5" <?php if($access_type == "T1" && $circ_count >= 5){ echo "style=\"display:block\""; }else{ echo "style=\"display:none\""; } ?> >
	<table style="border-collapse:collapse;">
	<tr><td>Serial Interface 5: <input type="text" name="SERIAL5" value="<?php if($access_type == "T1"){ echo $serial[5]; }?>"/></td><td>T1 Circuit ID 5: <input type="text" name="CID5_T1" value="<?php if($access_type == "T1"){ echo $circ_cid[5]; }?>"/></td></tr>
	</table>
	</div>
	<div id="TONE6" <?php if($access_type == "T1" && $circ_count >= 6){ echo "style=\"display:block\""; }else{ echo "style=\"display:none\""; } ?> >
	<table style="border-collapse:collapse;">
	<tr><td>Serial Interface 6: <input type="text" name="SERIAL6" value="<?php if($access_type == "T1"){ echo $serial[6]; }?>"/></td><td>T1 Circuit ID 6: <input type="text" name="CID6_T1" value="<?php if($access_type == "T1"){ echo $circ_cid[6]; }?>"/></td></tr>
	</table>
	</div>
	<div id="TONE7" <?php if($access_type == "T1" && $circ_count >= 7){ echo "style=\"display:block\""; }else{ echo "style=\"display:none\""; } ?> >
	<table style="border-collapse:collapse;">
	<tr><td>Serial Interface 7: <input type="text" name="SERIAL7" value="<?php if($access_type == "T1"){ echo $serial[7]; }?>"/></td><td>T1 Circuit ID 7: <input type="text" name="CID7_T1" value="<?php if($access_type == "T1"){ echo $circ_cid[7]; }?>"/></td></tr>
	</table>
	</div>
	<div id="TONE8" <?php if($access_type == "T1" && $circ_count >= 8){ echo "style=\"display:block\""; }else{ echo "style=\"display:none\""; } ?> >
	<table style="border-collapse:collapse;">
	<tr><td>Serial Interface 8: <input type="text" name="SERIAL8" value="<?php if($access_type == "T1"){ echo $serial[8]; }?>"/></td><td>T1 Circuit ID 8: <input type="text" name="CID7_T1" value="<?php if($access_type == "T1"){ echo $circ_cid[8]; }?>"/></td></tr>
	</table>
	</div>


	<!--EFM CIRCUIT ID DIVs -->
	<div id="EFMP1" <?php if($access_type == "EFM" && $circ_count >= 1){ echo "style=\"display:block\""; }else{ echo "style=\"display:none\""; } ?> >
	<h5 style="text-decoration:underline;">EFM CIRCUIT INFORMATION</h5>
	<table><tr><td>EFM Switch</td><td>HSL #</td><td>VLAN ID</td><td>CPE IP Address</td><td>Committed Bandwidth</td></tr>
	<tr><td class="form"><input type="text" name="EFM_SWITCH" <?php if($access_type == "EFM"){ highlight_input($efm_switch); }?> value="<?=$_POST['EFM_SWITCH']?>"/></td>
	<td class="form"><input type="text" name="HSL_EFM" size="10" <?php if($access_type == "EFM"){ highlight_input($hsl); }?> value="<?=$_POST['HSL_EFM']?>"/></td>
	<td class="form"><input type="text" name="VLAN_EFM" size="10" <?php if($access_type == "EFM"){ highlight_input($vlan); }?> value="<?=$_POST['VLAN_EFM']?>"/></td>
	<td class="form"><input type="text" name="EFM_IP" <?php if($access_type == "EFM"){ highlight_input($efm_ip); }?> value="<?=$_POST['EFM_IP']?>"/></td>
	<td class="form"><select name="rate_limit_EFM">
		<option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
		<option value="2" <?php if($committed_bw == 2){ echo "selected"; } ?> >2</option>
		<option value="4" <?php if($committed_bw == 4){ echo "selected"; } ?> >4</option>
		<option value="6" <?php if($committed_bw == 6){ echo "selected"; } ?> >6</option>
		<option value="8" <?php if($committed_bw == 8){ echo "selected"; } ?> >8</option>
		<option value="10" <?php if($committed_bw == 10){ echo "selected"; } ?> >10</option>
		<option value="15" <?php if($committed_bw == 15){ echo "selected"; } ?> >15</option>
		<option value="20" <?php if($committed_bw == 20){ echo "selected"; } ?> >20</option>
		<option value="25" <?php if($committed_bw == 25){ echo "selected"; } ?> >25</option>
		<option value="30" <?php if($committed_bw == 30){ echo "selected"; } ?> >30</option>
		<option value="35" <?php if($committed_bw == 35){ echo "selected"; } ?> >35</option>
		<option value="40" <?php if($committed_bw == 40){ echo "selected"; } ?> >40</option>
		<option value="45" <?php if($committed_bw == 45){ echo "selected"; } ?> >45</option>
		<option value="50" <?php if($committed_bw == 50){ echo "selected"; } ?> >50</option>
		<option value="100" <?php if($committed_bw == 100){ echo "selected"; } ?> >100</option>
	</select></td></tr></table>
	<table style="border-collapse:collapse;">
	<tr><td>EFM MLP 1: <input type="text" name="MLP1" size="10" value="<?php if($access_type == "EFM"){ echo $mlp[1]; }?>"/></td><td>EFM Circuit ID 1: <input type="text" name="CID1_EFM" value="<?php if($access_type == "EFM"){ echo $efm_cid[1]; }?>"/></td></tr>
	</table>
	</div>
	<div id="EFMP2" <?php if($access_type == "EFM" && $circ_count >= 2){ echo "style=\"display:block\""; }else{ echo "style=\"display:none\""; } ?> >
	<table style="border-collapse:collapse;">
	<tr><td>EFM MLP 2: <input type="text" name="MLP2" size="10" value="<?php if($access_type == "EFM"){ echo $mlp[2]; }?>"/></td><td>EFM Circuit ID 2: <input type="text" name="CID2_EFM" value="<?php if($access_type == "EFM"){ echo $efm_cid[2]; }?>"/></td></tr>
	</table>
	</div>
	<div id="EFMP3" <?php if($access_type == "EFM" && $circ_count >= 3){ echo "style=\"display:block\""; }else{ echo "style=\"display:none\""; } ?> >
	<table style="border-collapse:collapse;">
	<tr><td>EFM MLP 3: <input type="text" name="MLP3" size="10" value="<?php if($access_type == "EFM"){ echo $mlp[3]; }?>"/></td><td>EFM Circuit ID 3: <input type="text" name="CID3_EFM" value="<?php if($access_type == "EFM"){ echo $efm_cid[3]; }?>"/></td></tr>
	</table>
	</div>
	<div id="EFMP4" <?php if($access_type == "EFM" && $circ_count >= 4){ echo "style=\"display:block\""; }else{ echo "style=\"display:none\""; } ?> >
	<table style="border-collapse:collapse;">
	<tr><td>EFM MLP 4: <input type="text" name="MLP4" size="10" value="<?php if($access_type == "EFM"){ echo $mlp[4]; }?>"/></td><td>EFM Circuit ID 4: <input type="text" name="CID4_EFM" value="<?php if($access_type == "EFM"){ echo $efm_cid[4]; }?>"/></td></tr>
	</table>
	</div>
	<div id="EFMP5" <?php if($access_type == "EFM" && $circ_count >= 5){ echo "style=\"display:block\""; }else{ echo "style=\"display:none\""; } ?> >
	<table style="border-collapse:collapse;">
	<tr><td>EFM MLP 5: <input type="text" name="MLP5" size="10" value="<?php if($access_type == "EFM"){ echo $mlp[5]; }?>"/></td><td>EFM Circuit ID 5: <input type="text" name="CID5_EFM" value="<?php if($access_type == "EFM"){ echo $efm_cid[5]; }?>"/></td></tr>
	</table>
	</div>
	<div id="EFMP6" <?php if($access_type == "EFM" && $circ_count >= 6){ echo "style=\"display:block\""; }else{ echo "style=\"display:none\""; } ?> >
	<table style="border-collapse:collapse;">
	<tr><td>EFM MLP 6: <input type="text" name="MLP6" size="10" value="<?php if($access_type == "EFM"){ echo $mlp[6]; }?>"/></td><td>EFM Circuit ID 6: <input type="text" name="CID6_EFM" value="<?php if($access_type == "EFM"){ echo $efm_cid[6]; }?>"/></td></tr>
	</table>
	</div>
	<div id="EFMP7" <?php if($access_type == "EFM" && $circ_count >= 7){ echo "style=\"display:block\""; }else{ echo "style=\"display:none\""; } ?> >
	<table style="border-collapse:collapse;">
	<tr><td>EFM MLP 7: <input type="text" name="MLP7" size="10" value="<?php if($access_type == "EFM"){ echo $mlp[7]; }?>"/></td><td>EFM Circuit ID 7: <input type="text" name="CID7_EFM" value="<?php if($access_type == "EFM"){ echo $efm_cid[7]; }?>"/></td></tr>
	</table>
	</div>
	<div id="EFMP8" <?php if($access_type == "EFM" && $circ_count >= 8){ echo "style=\"display:block\""; }else{ echo "style=\"display:none\""; } ?> >
	<table style="border-collapse:collapse;">
	<tr><td>EFM MLP 8: <input type="text" name="MLP8" size="10" value="<?php if($access_type == "EFM"){ echo $mlp[8]; }?>"/></td><td>EFM Circuit ID 8: <input type="text" name="CID8_EFM" value="<?php if($access_type == "EFM"){ echo $efm_cid[8]; }?>"/></td></tr>
	</table>
	</div>

	<!--ANALOG LINE DIVs -->
	<?php
	for ($i = 1; $i <= 48; $i++)
	{
		echo "<div id=\"ANLG" . $i . "\" ";
		if ($local_line_count >= $i) {
			echo "style=\"display:block\"";
		} else {
			echo "style=\"display:none\"";
		}
		echo " >";
		
		if ($i == 1) {
			echo "<h5 style=\"text-decoration:underline;\">ANALOG LINE INFORMATION</h5>";
		}    
			echo "<table style=\"border-collapse:collapse;\"><tr><td class=\"line\">Line " . $i . ":</td><td class=\"form\"><input type=\"text\" name=\"ANALOG" . $i . "\" maxlength=\"10\" size=\"10\" value=\"" . $local_line_tn[$i] . "\"/></td></tr></table></div>";   
	}
	?>

	</td><td  style="vertical-align:top;padding-top:20px;">

	<?php
	if ($order_num)
	{
		//only display synopsis if an order number was entered
		echo "<div>";
		echo "<strong>ORDER SYNOPSIS</strong><br />";
		
		if ($critical_error == 1) {
			echo "<textarea rows=\"8\" cols=\"35\" style=\"background-color:#F0F0F0;font-family:arial;font-size:0.875em;\" readonly>";
			echo "The system couldn't find enough information to provide a synopsis.\n\n";
			echo "You will need to provide any missing values manually in order to proceed.";
		} else {
			echo "<textarea rows=\"22\" cols=\"35\" style=\"background-color:#F0F0F0;font-family:arial;font-size:0.875em;\" readonly>";
			echo "The system found a $access_type account with $circ_count circuit(s).  The customer is expecting $committed_bw MB of bandwidth.  Based on this information, the system recommends an $device for this order.\n\n";
			echo "Please indicate which MODEL and WIC cards are installed in the DEVICE INFORMATION section to the left.\n\n";
			echo "The system found $local_line_count analog line(s) on the order, but doesn't know what type of voice service the customer needs.  Please indicate the Voice Service Type in the field above.";
		}
		echo "</textarea></div>";
	}
	?>

	</td></tr></table>
	<hr/>
	<table width="100%">
		<tr>
			<td align="left">
			<table>
			<tr>
		<td><input class="button" type="submit" value="Pre-install Config"  onclick="return show_confirm('preinstall');" name="submit1" id="tooltip" title="Generates device configs for PreInstall"></td>
		<td><input class="button" type="button" value="Create Enterprise Group" onclick="validateSubmitEG();" name="submit2" id="tooltip" title="Creates Enterprise Group on Broadsfot"></td>
		<td><input class="button" type="submit" value="Activation Config" onclick="return show_confirm('activate');" name="submit1" id="tooltip" title="Generates device configs for Activation"> </td>
		<td><input class="button" type="submit" value="Configure Broadsoft" onclick="return show_configure();" name="submit2" id="tooltip" title="To add analog/digital lines on Broadsoft"> </td>
		<td><input class="button" type="submit" value="Provision 3rd Party" onclick="return provision3rdParty();" id="tooltip" title="To provision 3rd party tools on Broadsoft"> </td>
		<td><input class="button" type="reset" value="Reset Form" id="tooltip" title="Resets values to default or pulled from server"></td>
			</tr>
			</table>
			</td>
			<td align="center">
			<table>
			<tr>
		<td align="right"><input class="button" type="button" value="Clean Broadsoft" onclick="cleanBroadsoft();" id="tooltip" title="Removes all Analog lines from Broadsoft"></td>
		<td align="right"><input class="button" type="button" value="Disconnect Broadsoft" onclick="disconnectBroadsoft();" id="tooltip" title="Removes the entire Enterprise Group from Broadsoft"></td>
			</tr>
			</table>
			</td>
	</tr>
	</table>
</form>

	<br /><br /><br />
	</div>
	</div>

</body>

<script>
function provision3rdParty(){
	
    if (document.f1.voice_type.value == "VOIC7") {
			alert("Warning!!\n\n" +"This functionality is not avaliable for TCPS (VPS) voice service type.");
			return false;
		}
	if(document.f1.acct_num.value == ""){
		alert("Acct# Missing!!");
		return false;
	}

	document.f1.action = "provision_third_party.php";
	document.f1.target = "new_windowthirdparty";
	window.open('','new_windowthirdparty','width=600,height=550,location=0,toolbar=0,status=0,menubar=0,resizable=1,scrollbars=1').focus();
	document.f1.submit();	

}

function disconnectBroadsoft() {
    	
	var Missing = "";
    var errorFieldIndex = 0;


    resetErrorFields(errorFieldArray);
    errorFieldArray = new Array();
    if (document.f1.voice_type.value == "VOIC7") {
			alert("Warning!!\n\n" +"This functionality is not avaliable for TCPS (VPS) voice service type.");
			return false;
		}
    if (confirm("Warning!!\n\n" + "This will remove the entire account for Acct# " + document.f1.acct_num.value + " from broadsoft. Do you want to proceed?")) {
        if (document.f1.acct_num.value == "") {
            errorFieldArray[errorFieldIndex++] = document.f1.acct_num;
            Missing = Missing + "Account Number\n";
        }
        if (Missing != "") {
            Missing = "No values provided for the following field(s):\n\n" + Missing;
            focusErrorFields(errorFieldArray);
            alert(Missing)
            return false;
        }

        document.f1.buttonClicked.value = "DB";
        document.f1.action = "config_gen_order.php";
        document.f1.target = "_self";
        document.f1.submit();
    }
}

function cleanBroadsoft()
{
	var Missing = "";
    var errorFieldIndex = 0;


    resetErrorFields(errorFieldArray);
    errorFieldArray = new Array();
	if(document.f1.voice_type.value == "VOIC7")
		{
			alert("Warning!!\n\n" +"This functionality is not avaliable for TCPS (VPS) voice service type.");
			return false;
		}
    if (confirm("Warning!!\n\n" + "This will remove all the Local Lines from Acct#  " + document.f1.acct_num.value + ". Do you want to proceed?"))
	{
        if (document.f1.acct_num.value == "")
		{
            errorFieldArray[errorFieldIndex++] = document.f1.acct_num;
            Missing = Missing + "Account Number\n";
        }
        if (Missing != "")
		{
            Missing = "No values provided for the following field(s):\n\n" + Missing;
            focusErrorFields(errorFieldArray);
            alert(Missing)
            return false;
        }
        document.f1.buttonClicked.value = "CB";
        document.f1.action = "config_gen_order.php";
        document.f1.target = "_self";
        document.f1.submit();
    }
}

function displayCEGResults()
{
    if (('<?php echo $provisionCEGResult->return->status?>'.length) > 0)
	{
        var displayMessage = "Create Enterprise Group ";
        if ('<?php echo $provisionCEGResult->return->status?>' == 'SUCCESS')
		{
            displayMessage += "Success!";
            displayMessage += "\nAcct#: <?php echo $provisionCEGResult->return->elementId?>\n";
        } else 
		{
            displayMessage += "Failure!!";
            displayMessage += "\nAcct#: <?php echo $provisionCEGResult->return->accountId?>\n";
        }
			displayMessage += "<?php echo "Status: ".$provisionCEGResult->return->status?>\n";
			displayMessage += "<?php echo "Message: ".str_replace("\n",  "", $provisionCEGResult->return->message)?>\n";
        if ('<?php echo $provisionCEGResult->return->status?>' == 'SUCCESS') 
		{
            if (displayMessage.indexOf("Duplicate account request") > 0)
                displayMessage += "\n<?php echo "Account was already provisioned on: ".$broadsoft?>";
            else
                displayMessage += "\n<?php echo "Provisioned on: ".$broadsoft?>";
        }
			alert(displayMessage);
    }
}

function displayCBResults() 
{
    if (('<?php echo $CBresult->status?>'.length) > 0)
	{
        var displayMessage = "Clean Broadsoft ";
        if ('<?php echo $CBresult->status?>' == 'SUCCESS')
		{
            displayMessage += "Success!";
            displayMessage += "\nAcct#: <?php echo $CBresult->elementId?>\n";
            displayMessage += "<?php echo "Status: ".$CBresult->status?>\n";
            displayMessage += "Message: Deleted all Local Lines!!";
        } else 
		{
            displayMessage += "Failure!!";
            displayMessage += "\nAcct#: <?php echo $CBresult->elementId?>\n";
            displayMessage += "<?php echo "Status: ".$CBresult->status?>\n";
            displayMessage += "<?php echo "Error: ".str_replace("\n",  "", $CBresult->errorDescription)?>\n";
        }
			alert(displayMessage);
    }
}

function displayDBResults()
{
    if (('<?php echo $DBresult->status?>'.length) > 0)
	{
        var displayMessage = "Disconnect Broadsoft ";
        if ('<?php echo $DBresult->status?>' == 'SUCCESS')
		{
            displayMessage += "Success!";
            displayMessage += "\nAcct#: <?php echo $DBresult->elementId?>\n";
            displayMessage += "<?php echo "Status: ".$DBresult->status?>\n";
            displayMessage += "Message: <?php echo $DBresult->message?>\n";
        } else
		{
            displayMessage += "Failure!!";
            displayMessage += "\nAcct#: <?php echo $DBresult->elementId?>\n";
            displayMessage += "<?php echo "Status: ".$DBresult->status?>\n";
            displayMessage += "<?php echo "Error: ".str_replace("\n",  "", $DBresult->errorDescription)?>\n";
        }
			alert(displayMessage);
    }
}

function show_configure() {
    
	var Missing = "";
    var errorFieldIndex = 0;
    var ValidationErrors = "";
    var phRegExp = /^\d{10}$/;

    resetErrorFields(errorFieldArray);
    errorFieldArray = new Array();
	
    if (document.f1.voice_type.value == "VOIC7") {
			alert("Warning!!\n\n" +"This functionality is not avaliable for TCPS (VPS) voice service type.");
			return false;
		}
	
    if (document.f1.voice_type.value.substring(4) == "0") {
        errorFieldArray[errorFieldIndex++] = document.f1.voice_type;
        Missing = Missing + "Voice Service Type\n";
    }
    
    if (document.f1.btn.value == "") {
        errorFieldArray[errorFieldIndex++] = document.f1.btn;
        Missing = Missing + "BTN\n";
    }
    if (document.f1.acct_num.value == "") {
        errorFieldArray[errorFieldIndex++] = document.f1.acct_num;
        Missing = Missing + "Account Number\n";
    }
    if (document.f1.acct_name.value == "") {
        errorFieldArray[errorFieldIndex++] = document.f1.acct_name;
        Missing = Missing + "Account Name\n";
    }
    if (document.f1.iad_name.value == "") {
        errorFieldArray[errorFieldIndex++] = document.f1.iad_name;
        Missing = Missing + "IAD Name\n";
    }
    if (document.f1.BWAS.value.substring(4) == "") {
        errorFieldArray[errorFieldIndex++] = document.f1.BWAS;
        Missing = Missing + "BWAS\n";
    }
    if (document.f1.voice_type.value != "VOIC1" && document.f1.voice_type.value != "VOIC0") {
        if (document.f1.call_capacity.value == "") {
            errorFieldArray[errorFieldIndex++] = document.f1.call_capacity;
            Missing = Missing + "Call Capacity\n";
        } else {
            if (numberRegExp.test(document.f1.call_capacity.value)) {
                if (document.f1.call_capacity.value < 1 || document.f1.call_capacity.value > 99) {
                    ValidationErrors = ValidationErrors + "Call Capacity needs to be between 1 and 99\n";
                    errorFieldArray[errorFieldIndex++] = document.f1.call_capacity;
                }
            } else {
                ValidationErrors = ValidationErrors + "Call Capacity needs to be a numeric value\n";
                errorFieldArray[errorFieldIndex++] = document.f1.call_capacity;
            }

        }
    }

	for(i=1; i<=document.f1.num_analog_lines_ANALOG.value.substr(4); i++)
	{
	if(document.f1['ANALOG'+i].value == "")
	{
		errorFieldArray[errorFieldIndex++] = document.f1['ANALOG'+i];
        Missing = Missing + "Phone Number# " + i + "\n";
	}
	else
			if(!phRegExp.test(document.f1['ANALOG'+i].value))
		{
			ValidationErrors += "Invalid phone number: " + document.f1['ANALOG'+i].value + " It should be a 10 digit number\n";
			errorFieldArray[errorFieldIndex++] = document.f1['ANALOG'+i];
       
		}
	}

    if (!phRegExp.test(document.f1.btn.value)) {
        ValidationErrors = ValidationErrors + "Invalid BTN: " + document.f1.btn.value + " It should be a 10 digit number\n";
        errorFieldArray[errorFieldIndex++] = document.f1.btn;
    }


    if (Missing != "") {
        Missing = "No values provided for the following field(s):\n\n" + Missing;
        if (errorFieldArray[0])
            focusErrorFields(errorFieldArray);
        alert(Missing)
        return false;
    }

    if (ValidationErrors != "") {
        ValidationErrors = "Found following validation errors:\n\n" + ValidationErrors;
        alert(ValidationErrors);
        if (errorFieldArray[0])
            focusErrorFields(errorFieldArray);
        return false;
    }

    document.f1.action = "configureBroadsoft.php"
    document.f1.target = "new_windowconfigureBroadsoft";
    var numberOfLines = document.f1.num_analog_lines_ANALOG.value.substring(4);
    var windowHt = 610;
    if (numberOfLines > 10)
        windowHt = windowHt + (numberOfLines - 10) * 38;

    document.f1.submit();
    window.open('', 'new_windowconfigureBroadsoft', 'width=1000,height=' + windowHt + ',location=0,toolbar=0,status=0,menubar=0,resizable=1,scrollbars=1').focus();
    return true;
}

function validateSubmitEG()
{
    if(document.f1.voice_type.value == "VOIC7")
		{
			alert("Warning!!\n\n" +"This functionality is not avaliable for TCPS (VPS) voice service type.");
			return false;
		}
	
	var Missing = "";
    var ValidationErrors = "";
    var errorFieldIndex = 0;

    resetErrorFields(errorFieldArray);
    errorFieldArray = new Array();
	
    if (document.f1.voice_type.value.substring(4) == "0") {
        errorFieldArray[errorFieldIndex++] = document.f1.voice_type;
        Missing = Missing + "Voice Service Type\n";
    }
    if (document.f1.call_agent.value.substring(4) == "0") {
        errorFieldArray[errorFieldIndex++] = document.f1.call_agent;
        Missing = Missing + "Market\n";
    }
    if (document.f1.bwns_profile.value == "") {
        errorFieldArray[errorFieldIndex++] = document.f1.bwns_profile;
        Missing = Missing + "BWNS Profile\n";
    }

    if (document.f1.acct_num.value == "") {
        errorFieldArray[errorFieldIndex++] = document.f1.acct_num;
        Missing = Missing + "Account Number\n";
    }
    if (document.f1.acct_name.value == "") {
        errorFieldArray[errorFieldIndex++] = document.f1.acct_name;
        Missing = Missing + "Account Name\n";
    }

    if (document.f1.voice_type.value != "VOIC1" && document.f1.voice_type.value != "VOIC0") 
	{
        if (document.f1.call_capacity.value == "") {
            errorFieldArray[errorFieldIndex++] = document.f1.call_capacity;
            Missing = Missing + "Call Capacity\n";
        } else 
		{
            if (numberRegExp.test(document.f1.call_capacity.value)) 
			{
                if (document.f1.call_capacity.value < 1 || document.f1.call_capacity.value > 99) 
				{
                    ValidationErrors = ValidationErrors + "Call Capacity needs to be between 1 and 99\n";
                    errorFieldArray[errorFieldIndex++] = document.f1.call_capacity;
                }
            } else 
			{
                ValidationErrors = ValidationErrors + "Call Capacity needs to be a numeric value\n";
                errorFieldArray[errorFieldIndex++] = document.f1.call_capacity;
            }

        }
    }

    if (Missing != "") 
	{
        Missing = "No values provided for the following field(s):\n\n" + Missing;
        alert(Missing);
        if (errorFieldArray[0])
            focusErrorFields(errorFieldArray);
			return false;
    }

    if (ValidationErrors != "") 
	{
        ValidationErrors = "Found following validation errors:\n\n" + ValidationErrors;
        alert(ValidationErrors);
        if (errorFieldArray[0])
            focusErrorFields(errorFieldArray);
			return false;
    }

    document.f1.buttonClicked.value = "CEG";
    document.f1.action = "config_gen_order.php";
    document.f1.target = "_self";
    document.f1.submit();

}

function focusErrorFields(errorFieldArray) 
{
    for (var i = 0; i < errorFieldArray.length; i++) 
	{
        errorFieldArray[i].style["background-color"] = "#B20000";
        errorFieldArray[i].style["color"] = "white";
    }
		errorFieldArray[0].focus();
}

function resetErrorFields(errorFieldArray)
{
    for (var i = 0; i < errorFieldArray.length; i++)
	{
        errorFieldArray[i].style["background-color"] = "#F2F2F2";
        errorFieldArray[i].style["color"] = "black";
    }
}

function handleVoiceType(voiceType)
{
	if(voiceType == "VOIC1")
		document.getElementById("CALLC").style.display = 'none';
	else
	{
		document.getElementById("CALLC").style.display = 'block';
		resetLines();
	}
}

//To remove all analog lines first.
function resetLines()
{
   for(i=1; i<49; i++)
   {
           document.getElementById("ANLG"+i).style.display = 'none';
   }

}

//if script enabled warning message hidden.
document.getElementById('nojs').style.display="none";
document.getElementById('fullpage').style.display="inline";
</script>

</html>
