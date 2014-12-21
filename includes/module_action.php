<? 
/*
    Copyright (C) 2013-2014 xtr4nge [_AT_] gmail.com

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/ 
?>
<?
//include "../login_check.php";
include "../../../config/config.php";
include "../_info_.php";
include "../../../functions.php";

include "options_config.php";

// Checking POST & GET variables...
if ($regex == 1) {
    regex_standard($_GET["service"], "../msg.php", $regex_extra);
    regex_standard($_GET["action"], "../msg.php", $regex_extra);
    regex_standard($_GET["page"], "../msg.php", $regex_extra);
    regex_standard($io_action, "../msg.php", $regex_extra);
    regex_standard($_GET["install"], "../msg.php", $regex_extra);
}

$service = $_GET['service'];
$action = $_GET['action'];
$page = $_GET['page'];
$install = $_GET['install'];

#Tor's TransPort
$_trans_port="9040";


if($service != "") {
    
    if ($action == "start") {
        
        $exec = "$bin_sed -i 's/TransListenAddress .*/TransListenAddress $io_in_ip/g' torrc";
        //exec("$bin_danger \"" . $exec . "\"", $output); //DEPRECATED
        $output = exec_fruitywifi($exec);
        
        $exec = "$bin_sed -i 's/DNSListenAddress .*/DNSListenAddress $io_in_ip/g' torrc";
        //exec("$bin_danger \"" . $exec . "\"", $output); //DEPRECATED
        $output = exec_fruitywifi($exec);
        
        // COPY LOG
        if ( 0 < filesize( $mod_logs ) ) {
            $exec = "$bin_cp $mod_logs $mod_logs_history/".gmdate("Ymd-H-i-s").".log";
            //exec("$bin_danger \"$exec\"" ); //DEPRECATED
            exec_fruitywifi($exec);
            
            $exec = "$bin_echo '' > $mod_logs";
            //exec("$bin_danger \"$exec\"" ); //DEPRECATED
            exec_fruitywifi($exec);
        }
    
        // ADD selected options
        $tmp = array_keys($mode_ngrep);
        for ($i=0; $i< count($tmp); $i++) {
             if ($mode_ngrep[$tmp[$i]][0] == "1") {
                $options .= " -" . $tmp[$i] . " " . $mode_ngrep[$tmp[$i]][2];
            }
        }
        
        // CHECK ROUTE
        $exec = "$bin_route|grep default";
        $ifRouteOn = exec($exec);
        if ($ifRouteOn == "") {
            $exec = "$bin_route add default gw $io_in_ip";
            //exec("$bin_danger \"$exec\"" ); //DEPRECATED
            exec_fruitywifi($exec);
        }

        $exec = "/etc/init.d/ntp restart";
        exec_fruitywifi($exec);

        $exec = "/etc/init.d/tor restart";
        //$exec = "$bin_tor --defaults-torrc /usr/share/FruityWifi/www/modules/tor/includes/torrc >/dev/null &";
        $exec = "$bin_tor -f $mod_path/includes/torrc >/dev/null &";
        //exec("$bin_danger \"$exec\"" ); //DEPRECATED
        exec_fruitywifi($exec);

        //---------------------------------
        $exec = "$bin_iptables -t nat -A PREROUTING -i $io_in_iface -p udp --dport 53 -j REDIRECT --to-ports 9053";
        //exec("$bin_danger \"$exec\"" ); //DEPRECATED
        exec_fruitywifi($exec);

        $exec = "$bin_iptables -t nat -A PREROUTING -i $io_in_iface -p tcp --syn -j REDIRECT --to-ports $_trans_port";
        //$exec = "$bin_iptables -t nat -A PREROUTING -i $io_in_iface ! -d $io_in_ip  -p tcp ! --dport 53 --syn -j REDIRECT --to-ports $_trans_port";
        //exec("$bin_danger \"$exec\"" ); //DEPRECATED
        exec_fruitywifi($exec);
        //---------------------------------
        
    } else if($action == "stop") {
        // STOP MODULE
        $exec = "$bin_killall $mod_name";
        //exec("$bin_danger \"$exec\"" ); //DEPRECATED
        exec_fruitywifi($exec);
        
        //------------------------------------
        $exec = "$bin_iptables -t nat -D PREROUTING -i $io_in_iface -p udp --dport 53 -j REDIRECT --to-ports 9053";
        //exec("$bin_danger \"$exec\"" ); //DEPRECATED
        exec_fruitywifi($exec);

        $exec = "$bin_iptables -t nat -D PREROUTING -i $io_in_iface -p tcp --syn -j REDIRECT --to-ports $_trans_port";
        //$exec = "$bin_iptables -t nat -D PREROUTING -i $io_in_iface ! -d $io_in_ip  -p tcp ! --dport 53 --syn -j REDIRECT --to-ports $_trans_port";
        //exec("$bin_danger \"$exec\"" ); //DEPRECATED
        exec_fruitywifi($exec);
        //------------------------------------
        
        // COPY LOG
        if ( 0 < filesize( $mod_logs ) ) {
            $exec = "$bin_cp $mod_logs $mod_logs_history/".gmdate("Ymd-H-i-s").".log";
            //exec("$bin_danger \"$exec\"" ); //DEPRECATED
            exec_fruitywifi($exec);
            
            $exec = "$bin_echo '' > $mod_logs";
            //exec("$bin_danger \"$exec\"" ); //DEPRECATED
            exec_fruitywifi($exec);
        }

    }

}

if ($install == "install_$mod_name") {

    $exec = "chmod 755 install.sh";
    //exec("$bin_danger \"$exec\"" ); //DEPRECATED
    exec_fruitywifi($exec);

    $exec = "$bin_sudo ./install.sh > $log_path/install.txt &";
    //exec("$bin_danger \"$exec\"" ); //DEPRECATED
    exec_fruitywifi($exec);

    header('Location: ../../install.php?module='.$mod_name);
    exit;
}

if ($page == "status") {
    header('Location: ../../../action.php');
} else {
    header('Location: ../../action.php?page='.$mod_name);
}

?>
