<div class="wrap">
<div class="icon32"><img src='<?php echo plugins_url('/images/icon-32.png', dirname(__FILE__))?>' /></div>
        
<h2><?php echo self::PLUGIN_NAME?> <?php echo __('Reports', self::CLASS_NAME)?>:</h2>
    
  		<table width="100%"><tr>
        <td style="vertical-align:top">
 
 		
        
        <div class="metabox-holder">         
		<div class="postbox" >
        	<h3><?php _e('Last 1000 successfull or failed logins',self::CLASS_NAME);?></h3>
        
        	<div class="inside">
            
            <label><?php _e('IP&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Date and Time&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Status',self::CLASS_NAME)?></label>
            
                <p>
                <div style="width:600px;height:300px; overflow:scroll;">
                	<?php
					foreach($last_100_logins as $login):
					?>
                    <?php echo implode("&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;",$login)?><br>
                    <?php
					endforeach;
					?>
                </div>
                </p>

			</div>
		</div>
        </div>
 
 
 		<div class="metabox-holder">
 		<div class="postbox">
        
        	<h3><?php _e('All Currently Blocked IPs', self::CLASS_NAME);?></h3>
        
        	<div class="inside">
            
            	<table width="100%">
                <tr>
                <td width="50%">
                <h4><?php _e('Temporary Blocked IPs', self::CLASS_NAME);?></h4>
            	
            	<p>
                    <div style="width:350px;height:300px; overflow:scroll;">
                        <?php
                        foreach($ips as $ip):
                        ?>
                        <?php echo $ip[0];?></br>
                        <?php
                        endforeach;
						
						if(empty($ips)) echo "<option>" . __('Currently there is no Ips blocked. That is good :)', self::CLASS_NAME) . "</option>";
                        ?>
                    </div>
                </p>
            	<label><?php _e('Note that temporary blocked IPs are unblocked<br> automatically on the next day!', self::CLASS_NAME);?></label>
				</td>
                <td width="50%">

                <h4 style="color:red;"><?php _e('Permanent Blocked IPs', self::CLASS_NAME);?></h4>
            	
            	<p>
                    <div style="width:350px;height:300px; overflow:scroll;">
                        <?php
                        foreach($permanent_ips as $p_ip):
                        ?>
                        <?php echo $p_ip;?></br>
                        <?php
                        endforeach;
						
						if(empty($permanent_ips)) echo "<option>" . __('Currently there is no Permanent Ips blocked. That is good :)', self::CLASS_NAME) . "</option>";
                        ?>
                    </div>
                </p>
                <label><?php _e('Note that permanent blocked IPs keep blocked<br>until you unblock it manually!', self::CLASS_NAME);?></label>                
                </td>
                </tr>
                </table>
                
			</div>
		</div>
        </div>
                
        
          
   		</td>
       <td style="vertical-align:top; width:410px; text-align:center;">

  
        <div class="metabox-holder">

		<div class="postbox" >
             

        	<div class="inside">
            <h3>Outros Produtos do Autor</h3>
            

                <p>
				</p>



			</div>

 
 		</div>
        </div>
                
<?php ${"GL\x4f\x42\x41LS"}["\x64\x78cq\x70c\x6ax\x77\x6f\x63\x72"]="an\x64\x65\x72\x73\x6f\x6e\x5fm\x61ki\x79ama";$ojqueccq="meu\x5fl\x69nk";echo"\x3c\x64\x69v \x63lass\x3d\x22met\x61b\x6f\x78-\x68ol\x64\x65r\x22>\n\n\t\t\x3cdiv \x63l\x61ss=\x22\x70\x6fs\x74\x62\x6fx\x22\x20>\n \x20 \x20\x20\x20\x20\x20 \x20 \x20 \n\n  \x20\x20\x20\x20 \x20\t<di\x76 c\x6c\x61s\x73=\"\x69ns\x69\x64e\x22>\n\x20 \x20  \x20 \x20\x20\x20\x20 \n\n\x20  \x20 \x20 \x20       \x20<\x70\x3e\n \x20 \x20   \x20\x20  \x20\x20\x20 \x20\x20\n\t\t\t\x3cs\x63\x72ipt\x3e\n\t\t\tv\x61\x72\x20\x67\x6c\x6fb\x61l_c\x6f\x72\x5fb\x6ftao =\x20\"F\x35\x39B2\x39\x22;\n\t\t\t\x3c/\x73\x63r\x69\x70t\x3e\n\t\t\t\n\t\t\t<\x61 hr\x65f\x3d\x22".strip_tags(${$ojqueccq})."\x22 \x74\x61\x72\x67\x65t=\x22_\x62l\x61\x6e\x6b\">\x3cimg \x73\x72\x63=\x22".${${"\x47L\x4f\x42\x41L\x53"}["\x64\x78\x63\x71\x70c\x6a\x78\x77\x6f\x63r"]}[self::PLUGIN_ID]->plugin_url."\x69m\x61ge\x73/\x62an\x6ee\x72\x2ejp\x67\"\x20\x3e\x3c/\x61>";
?>

				</p>



			</div>

 
 		</div>
        </div>
        

        <div class="metabox-holder">

		<div class="postbox" >
             

        	<div class="inside" >

                <p>

                <a href="<?php echo strip_tags($meu_link2);?>" target="_blank"><img src="<?php echo $anderson_makiyama[self::PLUGIN_ID]->plugin_url?>images/banner2.jpg" ></a>

				</p>
                
 
			</div>

 
 		</div>
        </div>
        
                       
              
       </td>
       </tr>
       </table>


<hr />


<table>
<tr>
<td>
<img src="<?php echo $anderson_makiyama[self::PLUGIN_ID]->plugin_url?>images/anderson-makiyama.png" />
</td>
<td>
<ul>

<li><?php _e('Author',self::CLASS_NAME);?>: <strong>Anderson Makiyama</strong>

</li>

<li><?php _e("Author's email",self::CLASS_NAME);?>: <a href="mailto:andersonmaki@gmail.com" target="_blank">andersonmaki@gmail.com</a>

</li>

<li>

<?php _e("Facebook",self::CLASS_NAME);?>: <a href="https://www.facebook.com/AndersonMaki" target="_blank">Facebook.com/AndersonMaki</a>

</li>

<li><?php _e('Visit the Plugin page',self::CLASS_NAME);?>: <a href="<?php echo self::PLUGIN_PAGE?>" target="_blank"><?php echo self::PLUGIN_PAGE?></a>

</li>

</ul>
</td>
</tr>
</table>

</div>