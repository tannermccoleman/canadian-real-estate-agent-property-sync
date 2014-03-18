
<style>

    .well {
        min-height: 20px;
        padding: 19px;
        margin-bottom: 20px;
        background-color: #f0f0f0;
        border: 1px solid #e3e3e3;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
        -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05)
    }

    .center {
        text-align: center;
        margin-left: auto;
        margin-right: auto;
        margin-bottom: auto;
        margin-top: auto;
    }		  


</style>
<br /><div class="wrap">
    <?php $iconurl = PURPLE_XMLS_URL . '/images/crea_sync.png'; ?>
    <div id="icon-crea_sync" class="icon32" style="background: transparent url(<?php echo($iconurl); ?>) no-repeat">
        <br />
    </div>
    <h2>CREA Property Synchronizer</h2>

    <?php
    creasync_account_settings_update_options();
    ?>


    <br />
    <div id='poststuff'><div class='postbox' >
            <h3 class='hndle'>Crea Property Synching Status</h3>
            <div class='inside export-target'>
                <form method="post" action="">
                    <table class="form-table">  
                        <tr valign="top">
                            <th scope="row">Enable/Disable CREA Property synchronizer </th>
                            <td>
                                <select name="creasync_status" class="" id="sc-language">
                                    <option value="Enable" <?php selected('Enable', get_option('creasync_status')); ?>>Enable</option>
                                    <option value="Disable" <?php selected('Disable', get_option('creasync_status')); ?>>Disable</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <?php submit_button(); ?>  
                </form>
            </div>
        </div>
    </div>

    <div id='poststuff'><div class='postbox' >
            <h3 class='hndle'>CREA Realator API authentication Settings</h3>
            <div class='inside export-target'>
                <form method="post" action="">
                    <?php settings_fields('sc-settings-group'); ?>
                    <table class="form-table">

                        <tr valign="top">
                            <th scope="row">Username</th>
                            <td><input type="text" class="regular-text" name="creasync_api_username" value="<?php echo get_option('creasync_api_username', 'CXLHfDVrziCfvwgCuL8nUahC'); ?>" /></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Password</th>
                            <td><input type="password" class="regular-text" name="creasync_api_password" value="<?php echo get_option('creasync_api_password', 'mFqMsCSPdnb5WO1gpEEtDCHH'); ?>" /></td>
                        </tr>      
                        <tr valign="top">
                            <th scope="row">Environment</th>
                            <td>
                                <select name="creasync_environment_url" class="" id="sc-language">
                                    <option value="http://data.crea.ca/Login.svc/Login" <?php selected('http://data.crea.ca/Login.svc/Login', get_option('creasync_environment_url')); ?>>Production Environment</option>
                                    <option value="http://sample.data.crea.ca/Login.svc/Login" <?php selected('http://sample.data.crea.ca/Login.svc/Login', get_option('creasync_environment_url')); ?>>Development Environment</option>
                                </select>
                            </td>
                        </tr>
                <!--        <tr valign="top">
                            <th scope="row">Template Location</th>
                            <td><input type="text" class="regular-text" name="sc-template" value="<?php echo get_option('sc-template', 'wp-content/plugins/creasync/template/'); ?>" /></td>
                        </tr>-->
                    </table>

                    <input  class="button button-primary"  type="submit" value="Save & Test Connection" />  
                </form>
            </div>
        </div></div>

    <div id='poststuff'><div class='postbox' >
            <h3 class='hndle'>Property Synching</h3>
            <div class='inside export-target'>
                <form method="post" action="">
                    <table class="form-table">

                        <tr valign="top">
                            <td>
                                <input type="hidden" name="sync_now" value="1" />
                                <input  class="button button-primary"  type="submit" value="Synchronize Now..!" />  </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
    <div id='poststuff'><div class='postbox' >
            <h3 class='hndle'>Upgrade To Pro</h3>
            <div class='inside export-target'>
                <div class="well center">	
                    <a href="https://www.purpleturtle.pro/cart.php?gid=20" target="_blank"><img src="<?php echo plugins_url('/images/crea_sync_update_now.png', __DIR__); ?>" ></a>
                    <p>If you interested in CREA property synchronization plugin, then get the <a href="https://www.purpleturtle.pro/cart.php?gid=20" target="_blank">Crea Property Synchronization Pro Version</a> .</p>
                    <H2>Premium Features </h2>
                    <p># Unlimited property synching.</p>
                    <p># Synching Agents also .</p>
                    <p># Automatically remove outdated properties.</p>
                </div>
            </div>
        </div>
    </div>
    <br><br>
    <div>
        <br>
        &copy; 2013 Purple Turtle Productions.</div>
</div>

