<?php if ( ! defined( 'ABSPATH' ) ) {exit;}?>
<div class="tab-pane" id="ContentH">

    <div class="col-lg-12">
        
        <h3 style="font-size:22px"><?php _e("Vulnerability check","wpinfecscan");?></h3>
        <br>
        
        <h4 style="font-size:22px"><span class="dashicons dashicons-admin-users" style="font-size: 28px;color:#20ad78;"></span> <?php _e("Brute Force Attack Resistance Testing","wpinfecscan");?></h4>
        <p><?php _e("This test uses a list of 5,000 commonly used passwords to see if a hacker can break the site's administrator and editor passwords with a brute force attack. If a user is found to be a victim of this scan, we recommend that the user's password be changed as soon as possible to a meaningless string of 14 or more characters and containing one-byte alphanumeric symbols.","wpinfecscan");?>
        </p>
        
        <button class="btn btn-danger" id='usertestbutton'><?php _e("Test user password tolerance","wpinfecscan");?></button>
        <div id="userscanningnow" style="display:none"><i class="fa fa-circle-o-notch fa-spin"></i> <?php _e("Investigating user password tolerance...","wpinfecscan");?></div>
        
        <table class="table" id="userpassvaln" style="margin-top:15px;width:100%;">
        
        </table>
        
        <hr>
        
        <h4 style="font-size:22px"><span class="dashicons dashicons-pressthis" style="font-size: 28px;color:#565656;"></span> <?php _e("CVSS Vulnerability check","wpinfecscan");?></h4>
        <p><?php _e("This vulnerability checker will check if there are valunability(over CVSS 7.5 point) in your site's plugin and wordpress. We search valunability form <a href='https://nvd.nist.gov/vuln' target='_blank'>NIST</a> database.","wpinfecscan");?>
        </p>

        <div style="height:150px;overflow:auto;margin-bottom:25px;border:solid 0px #eee">
            <div style="padding-top:5px;">
            
                <?php
                
                $httpst = "http://";
                if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                    $httpst = "https://";
                }
                elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
                    $httpst = "https://";
                }

                $options['ssl']['verify_peer']=false;
                $options['ssl']['verify_peer_name']=false;
                $res = @file_get_contents($httpst.'wp-doctor.work/getscandatapro/valnupdate2.php', false, stream_context_create($options));
                
                $resar = @json_decode($res,true);
                if($resar){
                    $allcount = $resar['allcount'];
                    
                    echo '<h5><span class="dashicons dashicons-plus-alt"></span><b>'.__(" Recentry add vulnerabilities","wpinfecscan").' ('.__("Number of all detectable vulnerabilities:","wpinfecscan").' '.$allcount.')</b></h5>';
                
                    echo '<table class="table">
                            <thead>
                                <tr>
                                    <th>'.__('WordPress/Plugin name', "wpinfecscan").'</th>
                                    <th>'.__('CVE3 Score', "wpinfecscan").'</th>
                                    <th>'.__('Add date', "wpinfecscan").'</th>
                                </tr>
                            </thead>
                            <tbody>';
                    foreach($resar['adddata'] as $items){
                        if(count($items)<4){
                            continue;
                        }
                        $title = '';
                        $cve3score = '';
                        $addate = '';
                        if(isset($items[0])){
                            $title = $items[0];
                        }
                        if($items[1]==='0'){
                            $cve3score = 'NA';
                        }else{
                            if(isset($items[1])){
                                if(is_numeric($items[1])){
                                    $cve3score = intval($items[1])*0.1;
                                }else{
                                    $cve3score = 'NA';
                                }
                            }else{
                                $cve3score = 'NA';
                            }
                        }
						
						$asper = " / 10";
						if($cve3score == 'NA'){
							$asper = "";
						}
                       
                        if(isset($items[3])){
                            $addate = $items[3];
                        }
                        if(! empty($title)){
                        echo "
                            <tr>
                                <td><b>".$title."</b></td>
                                <td>".$cve3score.$asper."</td>
                                <td>".$addate."</td>
                            </tr>";
                        }
                    
                    }  
                    
                    echo '</tbody></table>';
                    
                }
                
                ?>
                
            </div>
        </div>
        <br>
        <?php
        if($mydomain=="localhost" || filter_var($mydomain, FILTER_VALIDATE_IP)){
            echo "<p style='color:red'><b>".__("You cannot run vulnerability checker on localhost or IP.","wpinfecscan")."</b></p>";
            if(! ($alerttxt && strlen($alerttxt)>0)){
                echo "<br>";
            }
        }
        
        $autoipblockok=true;
        if($scanner->getpro() != 1){
            $autoipblockok=false;
        }
        ?>
        
        <div style="margin-bottom:20px;">
        <?php
        $valncheckok = false;
        if($mydomain=="localhost" || filter_var($mydomain, FILTER_VALIDATE_IP)){
        ?>
        <button class="btn btn-default" id=''><?php _e("Not available","wpinfecscan");?></button>
        <?php }else{ ?>
        
        <?php
        $lastchecktime = get_option( 'wpinfectscanner_valnchecktime', "" );
        $exptxt="";
        if(empty($lastchecktime)){
            $valncheckok = true;
            if($autoipblockok==false){
                $exptxt =  __("(You can check valunability one time for free.)","wpinfecscan");
            }
        ?>
        <button class="btn btn-danger" id='valtestbutton'><?php _e("Run vulnerability checker","wpinfecscan");?><?php echo $exptxt; ?></button>
        <?php }else{
        if($autoipblockok==false){
        ?>
        <button class="btn btn-default" id=''><?php _e("Not available (Please purchase for malware subscription to check valunability on your site)","wpinfecscan");?></button>
        <?php }else{
        $valncheckok = true;
        ?>
        <button class="btn btn-danger" id='valtestbutton'><?php _e("Run vulnerability checker","wpinfecscan");?><?php echo $exptxt; ?></button>
        <?php }}} ?>
        
        <div id="scanningnow" style="display:none"><i class="fa fa-circle-o-notch fa-spin"></i> <?php _e("Checking valunability now...","wpinfecscan");?></div>
        </div>
        <br>
        <?php
        
        if(! empty($lastchecktime)){
            echo "<h4 id='valntitle'>".__("Vulnerability check result","wpinfecscan")."(".$lastchecktime.")</h4>";
        }else{
            echo "<h4 id='valntitle'>".__("Vulnerability to check","wpinfecscan")."</h4>";
        }
        ?>
        <table class="table">
            <thead>
                <tr>
                    <th><?php _e('Name', "wpinfecscan") ?></th>
                    <th><?php _e('Type', "wpinfecscan") ?></th>
                    <th><?php _e('Version', "wpinfecscan") ?></th>
                    <th><?php _e('Status', "wpinfecscan") ?></th>
                    <th><?php _e('Valunability', "wpinfecscan") ?></th>
                </tr>
            </thead>
            <tbody id="tbodychecked">
            
            <?php
            
            $sitevdata = array();
            global $wp_version;
            $sitevdata[] = array("wordpress","wordpress",$wp_version,"WordPress");
            
            $my_plugin = WP_PLUGIN_DIR;
            $folders = glob($my_plugin."/*", GLOB_ONLYDIR);
            foreach ($folders as $folder) {
                $files = scandir($folder."/"); 
                $foundfile=false;
                $pluginfolder=basename($folder);
                $pluginname="";
                $pluginversion="";
                foreach($files as $file)
                {
                    $path_parts = pathinfo($folder."/".$file);
                    if(isset($path_parts['extension'])){
                        if(is_file($folder."/".$file) && $path_parts['extension']=="php"){
                            if (! @ini_get("auto_detect_line_endings")) {
                                @ini_set("auto_detect_line_endings", '1');
                            }
                            $fn = fopen($folder."/".$file,"r");
                            if($fn){
                                $readcount = 0;
                                while(! feof($fn))  {
                                    $result = fgets($fn);
                                    if(strpos($result,"Plugin Name:")!== false || strpos($result,"Plugin Name :")!== false){
                                        $foundfile=true;
                                        $pluginname=explode(":",$result);
                                        $pluginname=trim($pluginname[1]);
                                    }
                                    if(strpos($result,"Version:")!== false || strpos($result,"Version :")!== false){
                                        $foundfile=true;
                                        $pluginversion=explode(":",$result);
                                        $pluginversion=trim($pluginversion[1]);
                                    }
                                    $readcount++;
                                    if($readcount>20){
                                        break;
                                    }
                                }
                                fclose($fn);
                            }
                        }
                    }
                    if($foundfile){
                        break;
                    }
                }
                if($pluginfolder!="" && $pluginname!="" && $pluginversion!=""){
                    $sitevdata[] = array($pluginfolder,"plugin",$pluginversion,$pluginname);
                }
            }
            
            $lastcheckdata = get_option( 'wpinfectscanner_valncheck', -1 );
            if($lastcheckdata!=-1){
                $checkeddata = json_decode($lastcheckdata,false);
                if($checkeddata){
                    foreach($checkeddata as $cdata){
                        //var_dump($cdata);
                        
                        $type = $cdata[1];
                        $version = $cdata[2];
                        $valn=$cdata[3];
                        $thisname = $cdata[4];
                        $valntxt = __("No vulnerability","wpinfecscan");
                        $cvetxt = "-";
                        $icon="<span class='dashicons dashicons-yes' style='color:green'></span>";
                        if($valn!="0"){
                            $valntxt= __("Vulnerability found","wpinfecscan");
                            $cvetxt = "";
                            $valnar=explode(",",$valn);
                            for($vi=0;$vi<count($valnar);$vi++){
                                $cve=trim($valnar[$vi]);
                                if(! empty($cve)){
                                    $cvetxt .= "<a href='https://nvd.nist.gov/vuln/detail/".$cve."' target='_blank'>".$cve."</a><br>";
                                }
                            }
                            $icon="<span class='dashicons dashicons-no' style='color:red'></span>";
                        }
                        echo "
                        <tr class='valnonedata'>
                            <td>".$icon." <b>".$thisname."</b></td>
                            <td>".$type."</td>
                            <td>".$version."</td>
                            <td>".$valntxt."</td>
                            <td>".$cvetxt."</td>
                        </tr>";
                    }
                }
            }else{
            ?>
                
                <?php
                foreach ($sitevdata as $onesitevdata){
                    //$pluginfolder,"plugin",$pluginversion,$pluginname
                ?>
                    <tr class='valnonedata'>
                        <td><b><?php echo $onesitevdata[3];?></b></td>
                        <td><?php echo $onesitevdata[1];?></td>
                        <td><?php echo $onesitevdata[2];?></td>
                        <td><?php _e('Not checked', "wpinfecscan") ?></td>
                        <td>-</td>
                    </tr>
                <?php
                }
            }
            ?>
            </tbody>
        </table>
        <script>
            var userchecknum = 0;
            function checkuservaln(){
                jQuery('#usertestbutton').hide();
                jQuery('#userscanningnow').show();
                jQuery.ajax({
                   type: "POST",
                   url: "<?php echo admin_url( 'admin-ajax.php'); ?>",
                   data: "action=wpinfectscanner_userpasscheck&usernum="+userchecknum,
                   success: function(msg){
                       if(msg == "-1"){
                           alert('Error:<?php _e('Reinstalling the plugin that failed the inspection may correct the problem.', "wpinfecscan") ?>');
                       }else{
                           if(msg == "-2"){
                               //done
                               jQuery('#usertestbutton').hide();
                               jQuery('#userscanningnow').hide();
                               alert('<?php _e('The inspection has been completed!', "wpinfecscan") ?>');
                           }else{
                               var objJSON = JSON.parse(msg);
                               if(objJSON.length>0){
                                   
                                   if(objJSON[0]==false){
                                        jQuery('#userpassvaln').append('<tr><td><span class="dashicons dashicons-yes" style="color:green"></span> <b>'+objJSON[1]+'</b></td><td><?php _e('No vulnerabilities', "wpinfecscan") ?></td><td>--</td></tr>');
                                   }else{
                                       jQuery('#userpassvaln').append('<tr><td><span class="dashicons dashicons-no" style="color:red"></span> <b>'+objJSON[1]+'</b></td><td><?php _e('Password is weak. Password breached:', "wpinfecscan") ?>'+objJSON[3]+'</td><td><a href="<?= get_admin_url();?>/user-edit.php?user_id='+objJSON[2]+'" target="_blank"><?php _e('Fix now', "wpinfecscan") ?></a></td></tr>');
                                   }
                                  userchecknum = userchecknum+1;
                                  checkuservaln();
                               }else{
                                   alert('Error:<?php _e('Reinstalling the plugin that failed the inspection may correct the problem.', "wpinfecscan") ?>');
                               }
                               
                           }
                           
                       }
                       
                   }
                 });
            }
            jQuery('#usertestbutton').click(function() {
                checkuservaln();
            });
        </script>
        <?php if($valncheckok){ ?>
        <script>
            var senddata = '<?php echo str_rot13(bin2hex(json_encode($sitevdata))) ;?>';
            jQuery('#valtestbutton').click(function() {
                jQuery('#valtestbutton').hide();
                jQuery('#scanningnow').show();
                jQuery.ajax({
                   type: "POST",
                   url: "<?php echo admin_url( 'admin-ajax.php'); ?>",
                   data: "chackdata="+senddata+"&action=wpinfectscanner_valncheck",
                   success: function(msg){
                       //alert(msg);
                       if(msg =="blocked"){
                           alert("<?php echo __("Valunability check failed. Are your site running on global domain?",'wpinfecscan'); ?>");
                       }else{
                           var objJSON = JSON.parse(msg);
                           if(objJSON.length>0){
                               jQuery(".valnonedata").remove();
                               jQuery('#valntitle').html("<?php echo __("Valunability check result",'wpinfecscan'); ?>");
                               for (var i = 0, len = objJSON.length; i < len; ++i) {
                                     var onedata = objJSON[i];
                                     var valntxt = "<?php _e('No vulnerability', "wpinfecscan") ?>";
                                     var cvetxt = "-";
                                     var icon="<span class='dashicons dashicons-yes' style='color:green'></span>";
                                     if(onedata[3]!="0"){
                                         cvetxt = "";
                                         valntxt = "<?php _e('Vulnerability found', "wpinfecscan") ?>";
                                         var cvetext = onedata[3];
                                         var cvear = cvetext.split(',');
                                         for (var ii = 0, tlen = cvear.length; ii < tlen-1; ++ii) {
                                             cvetxt = cvetxt + "<a href='https://nvd.nist.gov/vuln/detail/"+cvear[ii]+"' target='_blank'>"+cvear[ii]+"</a><br>";
                                         }
                                         var icon="<span class='dashicons dashicons-no' style='color:red'></span>";
                                     }
                                     jQuery("#tbodychecked").append("<tr class='valnonedata'><td>"+icon+" <b>"+onedata[4]+"</b></td><td>"+onedata[1]+"</td><td>"+onedata[2]+"</td><td>"+valntxt+"</td><td>"+cvetxt+"</td></tr>");
                               }
                           }
                            
                       }
                       jQuery('#valtestbutton').hide();
                       jQuery('#scanningnow').hide();
                   }
                 });
            });
        </script>
        <?php } ?>
    </div>
</div>