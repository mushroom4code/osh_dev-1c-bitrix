<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

//$module_root = realpath(dirname(__FILE__)."/..");
//require_once( $module_root."/prolog.php" );
require_once( dirname(__FILE__)."/../prolog.php" );

IncludeModuleLangFile(__FILE__);

// messages
$install_status=CModule::IncludeModuleEx("askaron.pro1c");
// demo expired (3)
if( $install_status==3 )
{
	$APPLICATION->SetTitle(GetMessage("askaron_pro1c_title"));
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	CAdminMessage::ShowMessage(
		Array(
			"TYPE"=>"ERROR",
			"MESSAGE"=>GetMessage("askaron_pro1c_prolog_status_demo_expired"),
			"DETAILS"=>GetMessage("askaron_pro1c_prolog_buy_html"),
			"HTML"=>true
		)
	);
}
else
{

	$RIGHT=$APPLICATION->GetGroupRight("askaron.pro1c");
	if( $RIGHT=="D" )
	{
		$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
	}

	// Title
	$APPLICATION->SetTitle(GetMessage("askaron_pro1c_live_log_title"));
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	?>



	<?

// demo (2)
	if( $install_status==2 )
	{
		CAdminMessage::ShowMessage(
			Array(
				"TYPE"=>"OK",
				"MESSAGE"=>GetMessage("askaron_pro1c_prolog_status_demo"),
				"DETAILS"=>GetMessage("askaron_pro1c_prolog_buy_html"),
				"HTML"=>true
			)
		);
	}
	?>
	<?if (!CModule::IncludeModule('pull')):?>
		<?
		CAdminMessage::ShowMessage(
			Array(
				"TYPE"=>"ERROR",
				"MESSAGE"=>GetMessage("askaron_pro1c_live_log_pull_not_installed"),
				//"DETAILS"=>GetMessage("askaron_pro1c_prolog_buy_html"),
				"HTML"=>true
			)
		);
		?>
		<?if(@file_exists( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/pull/install/index.php") ):?>
			<br /><br />
			<?=GetMessage("askaron_pro1c_live_log_pull_install", array("#LANG#" => LANG ) );?>							
		<?endif?>

		
	<?else:?>

		<?CPullWatch::Add($USER->GetId(), 'ASKARON_PRO1C_LIVE_LOG');?>	
		<?
			if (isset($_GET['TEST']) && $_GET['TEST'] == "Y")
			{				
				CAskaronPro1c::TestLiveLog();
				$APPLICATION->RestartBuffer();
				echo "OK";
				die();
			}

			if ( $_REQUEST["action"] == "set_log" )
			{
				if ( $_REQUEST["value"] == "Y" ||  $_REQUEST["value"] == "N"  )
				{
					COption::SetOptionString("askaron.pro1c", "log",  $_REQUEST["value"] );
					$APPLICATION->RestartBuffer();
					echo $_REQUEST["value"];
					die();
				}
			}


			if ( $_REQUEST["action"] == "set_forbidden" )
			{
				if ( $_REQUEST["value"] == "Y" ||  $_REQUEST["value"] == "N"  )
				{
					COption::SetOptionString("askaron.pro1c", "forbidden",  $_REQUEST["value"] );
					$APPLICATION->RestartBuffer();
					echo $_REQUEST["value"];
					die();
				}
			}




		?>
		<?if ( COption::GetOptionString( "askaron.pro1c", "live_log") == "N" ):?>
		<?
			CAdminMessage::ShowMessage(
			Array(
				"TYPE"=>"ERROR",
				"MESSAGE"=>GetMessage("askaron_pro1c_live_log_off"),
				"DETAILS"=>GetMessage("askaron_pro1c_live_log_off_details", array("#LANG#" => LANG) ),
				"HTML"=>true
			)
		);?>
		<?endif?>

		<?if ( COption::GetOptionString( "askaron.pro1c", "delayed_actions") == "NO" ):?>
			<?
			CAdminMessage::ShowMessage(
				Array(
					"TYPE"=>"ERROR",
					"MESSAGE"=>GetMessage("askaron_pro1c_delayed_actions"),
					"DETAILS"=>GetMessage("askaron_pro1c_delayed_actions_details", array("#LANG#" => LANG) ),
					"HTML"=>true
				)
			);?>
		<?endif?>

		<table width="100%">
			<tr>
				<td>
					<button onclick="askaron_pro1c_live_log_test();"><?=GetMessage( "askaron_pro1c_live_log_test" )?></button>

					<button onclick="BX('askaron_pro1c_pull').innerHTML=''"><?=GetMessage( "askaron_pro1c_live_log_clean" )?></button>

					<span id="askaron_pro1c_counter"></span>
				</td>
				<td>
					<div style="float: right;">
						<label for="askaron_pro1c_set_forbidden"><input
								onclick="askaron_pro1c_set_forbidden( this );"
								id="askaron_pro1c_set_forbidden"
								type="checkbox"
								value="Y"
								<?if ( \COption::GetOptionString( "askaron.pro1c", "forbidden") == "Y" ):?>
									checked="checked"
								<?endif?>
						><?=GetMessage( "askaron_pro1c_live_log_set_forbidden" );?></label>
						<br>
						<label for="askaron_pro1c_set_log"><input
								onclick="askaron_pro1c_set_log( this );"
								id="askaron_pro1c_set_log"
								type="checkbox"
								value="Y"
								<?if ( \COption::GetOptionString( "askaron.pro1c", "log") == "Y" ):?>
									checked="checked"
								<?endif?>
						><?=GetMessage( "askaron_pro1c_live_log_set_log" );?></label>
					</div>
				</td>
			</tr>
		</table>


		<div style="width: 100%; min-height: 200px; background-color: #FFF; border: 1px solid; padding: 5px; margin: 10px 0;" id="askaron_pro1c_pull"></div>

		<?=BeginNote();?>
			<?=GetMessage("askaron_pro1c_live_log_message", array("#LANG#" => LANG ) );?>
		<?=EndNote();?>	
		
		
		<?=BeginNote();?>
			<?if ( CPullOptions::GetNginxStatus() ):?>
				<?=GetMessage("askaron_pro1c_live_log_warning_nginx", array("#LANG#" => LANG ) );?>
			<?else:?>
				<?=GetMessage("askaron_pro1c_live_log_warning", array("#LANG#" => LANG ) );?>
			<?endif?>	
		<?=EndNote();?>	
		

		<?=BeginNote();?>
			<?=GetMessage("askaron_pro1c_live_log_settings", array("#LANG#" => LANG ) );?>
		<?=EndNote();?>	
		
		<script type="text/javascript">
			var askaron_pro1c_live_log_count = 0;
			var askaron_pro1c_time_out_test = null;

			var askaron_pro1c_set_log = function( inp )
			{
				var value='N';
				if ( inp.checked )
				{
					value='Y';
				}

				var url = '?action=set_log&value='+value+'&lang=<?=LANG?>';

				BX.ajax({
					url: url,
					method: 'GET',
					onsuccess: function(data){
						if (data==="Y" || data==="N")
						{
							BX('askaron_pro1c_pull').innerHTML += '<pre>' +
								'<?=CUtil::addslashes( GetMessage( "askaron_pro1c_live_log_set_log_success" ) )?>' +
								': '+data+ '. '+
								'<?=CUtil::addslashes( GetMessage( "askaron_pro1c_live_log_help_in_settings" ) )?>' +'</pre>';
						}
					}
				});
				//alert( inp.checked );
			}

			var askaron_pro1c_set_forbidden = function( inp )
			{
				var value='N';
				if ( inp.checked )
				{
					value='Y';
				}

				var url = '?action=set_forbidden&value='+value+'&lang=<?=LANG?>';

				BX.ajax({
					url: url,
					method: 'GET',
					onsuccess: function(data){
						if (data==="Y" || data==="N")
						{
							BX('askaron_pro1c_pull').innerHTML += '<pre>' +
								'<?=CUtil::addslashes( GetMessage( "askaron_pro1c_live_log_set_log_success" ) )?>' +
								': '+data+ '. '+
								'<?=CUtil::addslashes( GetMessage( "askaron_pro1c_live_log_help_in_settings" ) )?>' +'</pre>';
						}
					}
				});

				//alert( inp.checked );
			}




			var askaron_pro1c_live_log_test = function()
			{
				<?if ( CPullOptions::GetNginxStatus() ):?>
					BX('askaron_pro1c_pull').innerHTML += '<pre><?=CUtil::addslashes( GetMessage( "askaron_pro1c_live_log_alert_message_nginx" ) )?></pre>';
					
					clearTimeout(askaron_pro1c_time_out_test);
					
					askaron_pro1c_time_out_test = setTimeout(function(){
						BX('askaron_pro1c_pull').innerHTML += '<pre><?=CUtil::addslashes( GetMessage( "askaron_pro1c_live_log_alert_message_nginx_error" ) )?></pre>';
					}, 5000);
					
				<?else:?>
					BX('askaron_pro1c_pull').innerHTML += '<pre><?=CUtil::addslashes( GetMessage( "askaron_pro1c_live_log_alert_message" ) )?></pre>';
				<?endif?>					
				BX.ajax({ url: '?TEST=Y&lang=<?=LANG?>', method: 'GET'});
			};

			var askaron_pro1c_onPullEvent = function( command, params )
			{
				clearTimeout(askaron_pro1c_time_out_test);
				
				if ( command === 'live_log' )
				{					
					askaron_pro1c_live_log_count++;

					var color = "#000";
					if ( params.URL.indexOf("type=sale") >= 0 )
					{
						color = "#060";
					}
					else if ( params.URL.indexOf("type=crm") >= 0 )
					{
						color = "#00A";
					}

					BX('askaron_pro1c_pull').innerHTML += '<pre style="color: ' + color + '">'
						+ askaron_pro1c_live_log_count + '.   ' + params.TIME+'    '+ BX.util.htmlspecialchars( params.URL )+'\n'+BX.util.htmlspecialchars( params.DATA )+''
						+ '</pre>';
					BX('askaron_pro1c_counter').innerHTML = "<?=GetMessage("askaron_pro1c_live_log_counter");?>" + askaron_pro1c_live_log_count;					
				}
			};

			if (BX.PULL && BX.PULL.getRevision)
			{
				// from 14.1.0
	   			BX.addCustomEvent("onPullEvent-askaron.pro1c", function( command, params )
				{
					askaron_pro1c_onPullEvent( command, params );
				});			   
			}
			else
			{
				// before 14.1.0
	   			BX.addCustomEvent("onPullEvent", function( module_id, command, params )
				{
					if ( module_id === "askaron.pro1c" )
					{
						askaron_pro1c_onPullEvent( command, params );
					}
				});
			}

			// from 14.0.0
			if (BX.PULL.extendWatch)
			{
				BX.PULL.extendWatch('ASKARON_PRO1C_LIVE_LOG');
			}
			
			<?if ( !CPullOptions::GetNginxStatus() ):?>
				var askaron_pro1c_updateState = function()
				{
					BX.PULL.updateState('askaron.pro1c', true);
					setTimeout('askaron_pro1c_updateState()', 5000);
				}			
			
				// fast update
				askaron_pro1c_updateState();
			<?endif?>
			
		</script>
	<?endif?>
		
<?}?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>