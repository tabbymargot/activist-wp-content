<?php

if ( class_exists( 'ICWP_WPSF_Processor_Plugin_SetupWizard', false ) ) {
	return;
}

require_once( dirname( __FILE__ ).DIRECTORY_SEPARATOR.'base_wpsf.php' );

/**
 * @uses php 5.4+
 * Class ICWP_WPSF_Processor_Plugin_SetupWizard
 */
class ICWP_WPSF_Processor_Plugin_SetupWizard extends ICWP_WPSF_Processor_BaseWpsf {

	/**
	 * @var string
	 */
	private $sCurrentWizard;

	/**
	 */
	public function run() {
		add_action( 'init', array( $this, 'onWpInit' ), 0 );
	}

	public function onWpInit() {
		if ( $this->loadWpUsers()->isUserAdmin() ) {
			$this->loadWizard( (string)$this->loadDP()->query( 'wizard', '' ) );
		}
	}

	/**
	 * @param string $sWizard
	 * @throws Exception
	 */
	protected function loadWizard( $sWizard ) {

		$sContent = '';
		$this->setCurrentWizard( $sWizard );
		switch ( $sWizard ) {
			case 'welcome':
				$sContent = $this->renderWizardWelcome();
				break;
			case 'import':
				$sContent = $this->renderWizardImport();
				break;
			default:
				$this->loadWp()->redirectToAdmin();
				break;
		}
		echo $sContent;
		die();
	}

	/**
	 */
	public function ajaxSetupWizardSteps() {
		$oDP = $this->loadDP();

		$this->setCurrentWizard( $oDP->post( 'wizard_slug' ) );
		$aNextStep = $this->getWizardNextStep( $oDP->post( 'wizard_steps' ), $oDP->post( 'current_index' ) );

		return $this->getFeature()
					->sendAjaxResponse(
						true,
						array( 'next_step' => $aNextStep )
					);
	}

	public function ajaxSetupWizardContent() {
		$oDP = $this->loadDP();

		$this->loadAutoload(); // for Response
		switch ( $oDP->post( 'wizard-step' ) ) {

			case 'admin_access_restriction_verify':
				$oResponse = $this->wizardSecurityAdminVerify();
				break;

			case 'license':
				$oResponse = $this->wizardLicense();
				break;

			case 'import_options':
				$oResponse = $this->wizardImportOptions();
				break;

			case 'admin_access_restriction':
				$oResponse = $this->wizardSecurityAdmin();
				break;

			case 'audit_trail':
				$oResponse = $this->wizardAuditTrail();
				break;

			case 'ips':
				$oResponse = $this->wizardIps();
				break;

			case 'comments_filter':
				$oResponse = $this->wizardCommentsFilter();
				break;

			case 'login_protect':
				$oResponse = $this->wizardLoginProtect();
				break;

			case 'optin':
				$oResponse = $this->wizardOptin();
				break;

			default:
				$oResponse = new \FernleafSystems\Utilities\Response();
				$oResponse->setSuccessful( false )
						  ->setMessageText( _wpsf__( 'Unknown request' ) );
				break;
		}

		$sMessage = $oResponse->getMessageText();
		if ( $oResponse->successful() ) {
			$sMessage .= '<br />'.sprintf( _wpsf__( 'Please click %s to continue.' ), _wpsf__( 'Next' ) );
		}
		else {
			$sMessage = sprintf( '%s: %s', _wpsf__( 'Error' ), $sMessage );
		}

		$aData = $oResponse->getData();
		$aData[ 'message' ] = $sMessage;

		$this->getFeature()
			 ->sendAjaxResponse( $oResponse->successful(), $aData );
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	protected function renderWizardImport() {
		return $this->renderWizard();
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	protected function renderWizardWelcome() {
		return $this->renderWizard();
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	protected function renderWizard() {
		/** @var ICWP_WPSF_FeatureHandler_Plugin $oFO */
		$oFO = $this->getFeature();
		$oCon = $this->getController();

		$sMessage = $this->loadAdminNoticesProcessor()
						 ->flushFlashMessage()
						 ->getRawFlashMessageText();

		$aDisplayData = array(
			'strings' => array(
				'welcome'         => _wpsf__( 'Welcome' ),
				'time_remaining'  => _wpsf__( 'Time Remaining' ),
				'calculating'     => _wpsf__( 'Calculating' ).' ...',
				'seconds'         => strtolower( _wpsf__( 'Seconds' ) ),
				'login_expired'   => _wpsf__( 'Login Expired' ),
				'verify_my_login' => _wpsf__( 'Verify My Login' ),
				'more_info'       => _wpsf__( 'More Info' ),
				'what_is_this'    => _wpsf__( 'What is this?' ),
				'message'         => $sMessage,
				'page_title'      => sprintf( _wpsf__( '%s Setup Wizard' ), $oCon->getHumanName() )
			),
			'data'    => array(
				'wizard_slug'       => $this->getCurrentWizard(),
				'wizard_steps'      => json_encode( $this->determineWizardSteps() ),
				'wizard_first_step' => json_encode( $this->getWizardFirstStep() ),
			),
			'hrefs'   => array(
				'form_action'      => $this->loadDataProcessor()->getRequestUri(),
				'css_bootstrap'    => $oCon->getPluginUrl_Css( 'bootstrap3.min.css' ),
				'css_pages'        => $oCon->getPluginUrl_Css( 'pages.css' ),
				'css_steps'        => $oCon->getPluginUrl_Css( 'jquery.steps.css' ),
				'css_fancybox'     => $oCon->getPluginUrl_Css( 'jquery.fancybox.min.css' ),
				'css_globalplugin' => $oCon->getPluginUrl_Css( 'global-plugin.css' ),
				'css_wizard'       => $oCon->getPluginUrl_Css( 'wizard.css' ),
				'js_jquery'        => $this->loadWpIncludes()->getUrl_Jquery(),
				'js_bootstrap'     => $oCon->getPluginUrl_Js( 'bootstrap3.min.js' ),
				'js_fancybox'      => $oCon->getPluginUrl_Js( 'jquery.fancybox.min.js' ),
				'js_globalplugin'  => $oCon->getPluginUrl_Js( 'global-plugin.js' ),
				'js_steps'         => $oCon->getPluginUrl_Js( 'jquery.steps.min.js' ),
				'js_wizard'        => $oCon->getPluginUrl_Js( 'wizard.js' ),
				'shield_logo'      => 'https://plugins.svn.wordpress.org/wp-simple-firewall/assets/banner-1544x500-transparent.png',
				'what_is_this'     => 'https://icontrolwp.freshdesk.com/support/solutions/articles/3000064840',
				'favicon'          => $oCon->getPluginUrl_Image( 'pluginlogo_24x24.png' ),
			),
			'ajax'    => array(
				'content'       => $oFO->getBaseAjaxActionRenderData( 'SetupWizardContent' ),
				'steps'         => $oFO->getBaseAjaxActionRenderData( 'SetupWizardSteps' ),
				'steps_as_json' => $oFO->getBaseAjaxActionRenderData( 'SetupWizardSteps', true ),
			)
		);

		return $this->loadRenderer( $this->getController()->getPath_Templates() )
					->setTemplate( 'pages/wizard.twig' )
					->setRenderVars( $aDisplayData )
					->setTemplateEngineTwig()
					->render();
	}

	/**
	 * @return string[]
	 */
	protected function determineWizardSteps() {

		switch ( $this->getCurrentWizard() ) {
			case 'welcome':
				$aSteps = $this->determineWizardSteps_Welcome();
				break;
			case 'import':
				$aSteps = $this->determineWizardSteps_Import();
				break;
			default:
				$aSteps = array();
				break;
		}

		return $aSteps;
	}

	/**
	 * @return string[]
	 */
	private function determineWizardSteps_Import() {
		/** @var ICWP_WPSF_FeatureHandler_Plugin $oFO */
		$oFO = $this->getFeature();

		// Special case: user doesn't meet even the basic plugin admin permissions
		if ( !$oFO->getController()->getUserCanBasePerms() ) {
			return array( 'no_access' );
		}

		$aStepsSlugs = array(
			'import_start',
			'import_options',
			'import_finished',
		);

		return $aStepsSlugs;
	}

	/**
	 * @return string[]
	 */
	private function determineWizardSteps_Welcome() {
		/** @var ICWP_WPSF_FeatureHandler_Plugin $oFO */
		$oFO = $this->getFeature();

		// Special case: user doesn't meet even the basic plugin admin permissions
		if ( !$oFO->getController()->getUserCanBasePerms() ) {
			return array( 'no_access' );
		}

		$aStepsSlugs = array( 'welcome' );
		if ( !$oFO->isPremium() ) {
//			$aStepsSlugs[] = 'license'; not showing it for now
		}

		if ( $oFO->isPremium() ) {
			$aStepsSlugs[] = 'import_options';
		}

		if ( !$this->getController()->getModule( 'admin_access_restriction' )->getIsMainFeatureEnabled() ) {
			$aStepsSlugs[] = 'admin_access_restriction';
		}

		/** @var ICWP_WPSF_FeatureHandler_AuditTrail $oModule */
		$oModule = $this->getController()->getModule( 'audit_trail' );
		if ( !$oModule->getIsMainFeatureEnabled() ) {
			$aStepsSlugs[] = 'audit_trail';
		}

		if ( !$this->getController()->getModule( 'ips' )->getIsMainFeatureEnabled() ) {
			$aStepsSlugs[] = 'ips';
		}

		/** @var ICWP_WPSF_FeatureHandler_LoginProtect $oModule */
		$oModule = $this->getController()->getModule( 'login_protect' );
		if ( !( $oModule->getIsMainFeatureEnabled() && $oModule->isEnabledGaspCheck() ) ) {
			$aStepsSlugs[] = 'login_protect';
		}

		/** @var ICWP_WPSF_FeatureHandler_CommentsFilter $oModule */
		$oModule = $this->getController()->getModule( 'comments_filter' );
		if ( !( $oModule->getIsMainFeatureEnabled() && $oModule->isEnabledGaspCheck() ) ) {
			$aStepsSlugs[] = 'comments_filter';
		}

		$aStepsSlugs[] = 'how_shield_works';
		$aStepsSlugs[] = 'optin';

		if ( !$oFO->isPremium() ) {
			$aStepsSlugs[] = 'import_options';
		}

		$aStepsSlugs[] = 'thankyou';
		return $aStepsSlugs;
	}

	/**
	 * @return array
	 */
	protected function getWizardFirstStep() {
		return $this->getWizardNextStep( $this->determineWizardSteps(), -1 );
	}

	/**
	 * @param array $aAllSteps
	 * @param int   $nCurrentStep
	 * @return array
	 */
	protected function getWizardNextStep( $aAllSteps, $nCurrentStep ) {

		// The assumption here is that the step data exists!
		$aStepData = $this->getWizardSteps()[ $aAllSteps[ $nCurrentStep + 1 ] ];

		$bRestrictedAccess = !isset( $aStepData[ 'restricted_access' ] ) || $aStepData[ 'restricted_access' ];
		try {
			if ( !$bRestrictedAccess || $this->getController()->getHasPermissionToManage() ) {
				$aData = $this->getRenderDataForStep( $aStepData[ 'slug' ] );
				$aStepData[ 'content' ] = $this->renderWizardStep( $aStepData[ 'slug' ], $aData );
			}
			else {
				$aStepData[ 'content' ] = $this->renderSecurityAdminVerifyWizardStep( $nCurrentStep );
			}
		}
		catch ( Exception $oE ) {
			$aStepData[ 'content' ] = 'Content could not be displayed due to error: '.$oE->getMessage();
		}

		return $aStepData;
	}

	/**
	 * @param int $nIndex
	 * @return string
	 * @throws Exception
	 */
	protected function renderSecurityAdminVerifyWizardStep( $nIndex ) {
		return $this->renderWizardStep( 'admin_access_restriction_verify', array( 'current_index' => $nIndex ) );
	}

	/**
	 * @param string $sSlug
	 * @return array
	 */
	protected function getRenderDataForStep( $sSlug ) {
		/** @var ICWP_WPSF_FeatureHandler_Plugin $oFO */
		$oFO = $this->getFeature();
		$oConn = $this->getController();

		$aData = array(
			'flags' => array(
				'is_premium' => $oFO->isPremium()
			),
			'hrefs' => array(
				'dashboard' => $oFO->getFeatureAdminPageUrl(),
				'gopro'     => 'http://icwp.io/ap',
			),
			'imgs'  => array(),
		);

		$aAdd = array();

		switch ( $sSlug ) {
			case 'license':
				break;
			case 'import_options':
				$aAdd = array(
					'hrefs' => array(
						'blog_importexport' => 'http://icwp.io/av'
					),
					'imgs' => array(
						'shieldnetworkmini' => $oConn->getPluginUrl_Image( 'shield/shieldnetworkmini.png' ),
					)
				);
				break;

			case 'optin':
				$oUser = $this->loadWpUsers()->getCurrentWpUser();
				$aAdd = array(
					'data' => array(
						'name'       => $oUser->first_name,
						'user_email' => $oUser->user_email
					)
				);
				break;

			case 'no_access':
			case 'thankyou':
				break;

			case 'how_shield_works':
				$aAdd = array(
					'imgs'     => array(
						'how_shield_works' => $oConn->getPluginUrl_Image( 'wizard/general-shield_where.png' ),
						'modules'          => $oConn->getPluginUrl_Image( 'wizard/general-shield_modules.png' ),
						'options'          => $oConn->getPluginUrl_Image( 'wizard/general-shield_options.png' ),
						'help'             => $oConn->getPluginUrl_Image( 'wizard/general-shield_help.png' ),
						'actions'          => $oConn->getPluginUrl_Image( 'wizard/general-shield_actions.png' ),
						'module_onoff'     => $oConn->getPluginUrl_Image( 'wizard/general-module_onoff.png' ),
						'option_help'      => $oConn->getPluginUrl_Image( 'wizard/general-option_help.png' ),
					),
					'headings' => array(
						'how_shield_works' => _wpsf__( 'Where to find Shield' ),
						'modules'          => _wpsf__( 'Accessing Each Module' ),
						'options'          => _wpsf__( 'Accessing Options' ),
						'help'             => _wpsf__( 'Finding Help' ),
						'actions'          => _wpsf__( 'Actions (not Options)' ),
						'module_onoff'     => _wpsf__( 'Module On/Off Switch' ),
						'option_help'      => _wpsf__( 'Help For Each Option' ),
					),
					'captions' => array(
						'how_shield_works' => _wpsf__( "You'll find the main Shield Security setting in the left-hand WordPress menu." ),
						'modules'          => _wpsf__( 'Shield is split up into independent modules for accessing the options of each feature.' ),
						'options'          => _wpsf__( 'When you load a module, you can access the options by clicking on the Options Panel link.' ),
						'help'             => _wpsf__( 'Each module also has a brief overview help section - there is more in-depth help available.' ),
						'actions'          => _wpsf__( 'Certain modules have extra actions and features, e.g. Audit Trail Viewer.' )
											  .' '._wpsf__( 'Note: Not all modules have the actions section' ),
						'module_onoff'     => _wpsf__( 'Each module has an Enable/Disable checkbox to turn on/off all processing for that module' ),
						'option_help'      => _wpsf__( 'To help you understand each option, most of them have a more info link, and/or a blog link, to read more' ),
					),
				);
				break;
			default:
				break;
		}

		return $this->loadDP()->mergeArraysRecursive( $aData, $aAdd );
	}

	/**
	 * @param string $sSlug
	 * @param array  $aRenderData
	 * @return string
	 * @throws Exception
	 */
	protected function renderWizardStep( $sSlug, $aRenderData = array() ) {
		return $this->loadRenderer( $this->getController()->getPath_Templates() )
					->setTemplate( sprintf( 'wizard/slide-%s.twig', $sSlug ) )
					->setRenderVars( $aRenderData )
					->setTemplateEngineTwig()
					->render();
	}

	/**
	 * @return array[]
	 */
	private function getWizardSteps() {
		$aStandard = array(
			'import_start'             => array(
				'title'             => _wpsf__( 'Start Import' ),
				'slug'              => 'import_start',
				'content'           => '',
				'restricted_access' => false
			),
			'import_finished'          => array(
				'title'             => _wpsf__( 'Import Finished' ),
				'slug'              => 'import_finished',
				'content'           => '',
				'restricted_access' => false
			),
			'no_access'                => array(
				'title'             => _wpsf__( 'No Access' ),
				'slug'              => 'no_access',
				'content'           => '',
				'restricted_access' => false
			),
			'welcome'                  => array(
				'title'             => _wpsf__( 'Welcome' ),
				'slug'              => 'welcome',
				'content'           => '',
				'restricted_access' => false,
			),
			'license'                  => array(
				'title'   => _wpsf__( 'Go Pro' ),
				'slug'    => 'license',
				'content' => '',
			),
			'import_options'           => array(
				'title'   => _wpsf__( 'Import' ),
				'slug'    => 'import_options',
				'content' => '',
			),
			'admin_access_restriction' => array(
				'title'   => _wpsf__( 'Security Admin' ),
				'slug'    => 'admin_access_restriction',
				'content' => '',
			),
			'audit_trail'              => array(
				'title'   => _wpsf__( 'Audit Trail' ),
				'slug'    => 'audit_trail',
				'content' => '',
			),
			'ips'                      => array(
				'title'   => _wpsf__( 'IP Blacklist' ),
				'slug'    => 'ips',
				'content' => '',
			),
			'login_protect'            => array(
				'title'   => _wpsf__( 'Login Protection' ),
				'slug'    => 'login_protect',
				'content' => '',
			),
			'comments_filter'          => array(
				'title'   => _wpsf__( 'Comment SPAM' ),
				'slug'    => 'comments_filter',
				'content' => '',
			),
			'how_shield_works'         => array(
				'title'             => _wpsf__( 'How Shield Works' ),
				'slug'              => 'how_shield_works',
				'content'           => '',
				'restricted_access' => false,
			),
			'optin'                    => array(
				'title'   => _wpsf__( 'Join Us!' ),
				'slug'    => 'optin',
				'content' => '',
			),
			'thankyou'                 => array(
				'title'             => _wpsf__( 'Thank You' ),
				'slug'              => 'thankyou',
				'content'           => '',
				'restricted_access' => false,
			)
		);

		return $aStandard;
	}

	/**
	 * @return \FernleafSystems\Utilities\Response
	 */
	private function wizardLicense() {
		$sKey = $this->loadDP()->post( 'LicenseKey' );

		$bSuccess = false;
		if ( empty( $sKey ) ) {
			$sMessage = 'License key was empty.';
		}
		else {
			/** @var ICWP_WPSF_FeatureHandler_License $oModule */
			$oModule = $this->getController()->getModule( 'license' );
			try {
				$oModule->activateOfficialLicense( $sKey, true );
				if ( $oModule->hasValidWorkingLicense() ) {
					$bSuccess = true;
					$sMessage = _wpsf__( 'License key was accepted and installed successfully.' );
				}
				else {
					$sMessage = _wpsf__( 'License key was not accepted.' );
				}
			}
			catch ( Exception $oE ) {
				$sMessage = _wpsf__( $oE->getMessage() );
			}
		}

		$oResponse = new \FernleafSystems\Utilities\Response();
		return $oResponse->setSuccessful( $bSuccess )
						 ->setMessageText( $sMessage );
	}

	/**
	 * @return \FernleafSystems\Utilities\Response
	 */
	private function wizardImportOptions() {
		/** @var ICWP_WPSF_FeatureHandler_Plugin $oFO */
		$oFO = $this->getFeature();
		$oDP = $this->loadDP();

		$sMasterSiteUrl = $oDP->post( 'MasterSiteUrl' );
		$sSecretKey = $oDP->post( 'MasterSiteSecretKey' );
		$bEnabledNetwork = $oDP->post( 'ShieldNetworkCheck' ) === 'Y';

		/** @var ICWP_WPSF_Processor_Plugin $oProc */
		$oProc = $oFO->getProcessor();
		$nCode = $oProc->getSubProcessorImportExport()
					   ->runImport( $sMasterSiteUrl, $sSecretKey, $bEnabledNetwork, $sSiteResponse );

		$aErrors = array(
			_wpsf__( 'Options imported successfully to your site.' ), // success
			_wpsf__( 'Secret key was empty.' ),
			_wpsf__( 'Secret key was not 40 characters long.' ),
			_wpsf__( 'Secret key contains invalid characters - it should be letters and numbers only.' ),
			_wpsf__( 'Source site URL could not be parsed correctly.' ),
			_wpsf__( 'Could not parse the response from the site.' )
			.' '._wpsf__( 'Check the secret key is correct for the remote site.' ),
			_wpsf__( 'Failure response returned from the site.' ),
			sprintf( _wpsf__( 'Remote site responded with - %s' ), $sSiteResponse ),
			_wpsf__( 'Data returned from the site was empty.' )
		);

		$sMessage = isset( $aErrors[ $nCode ] ) ? $aErrors[ $nCode ] : 'Unknown Error';

		$oResponse = new \FernleafSystems\Utilities\Response();
		return $oResponse->setSuccessful( $nCode === 0 )
						 ->setMessageText( $sMessage );
	}

	/**
	 * @return \FernleafSystems\Utilities\Response
	 */
	private function wizardSecurityAdminVerify() {
		$sKey = $this->loadDP()->post( 'AccessKey' );

		$oResponse = new \FernleafSystems\Utilities\Response();

		$bSuccess = false;
		/** @var ICWP_WPSF_FeatureHandler_AdminAccessRestriction $oModule */
		$oModule = $this->getController()->getModule( 'admin_access_restriction' );

		$sMessage = '';
		if ( empty( $sKey ) ) {
			$sMessage = 'Security access key was empty.';
		}
		else if ( !$oModule->verifyAccessKey( $sKey ) ) {
			$sMessage = _wpsf__( 'Security Admin Key was not correct.' );
		}
		else {
			$bSuccess = true;
			$oModule->setPermissionToSubmit( true );
			$aData = array(
				'rerender' => true
			);
			$oResponse->setData( $aData );
		}

		return $oResponse->setSuccessful( $bSuccess )
						 ->setMessageText( $sMessage );
	}

	/**
	 * @return \FernleafSystems\Utilities\Response
	 */
	private function wizardSecurityAdmin() {
		$oDP = $this->loadDP();
		$sKey = $oDP->post( 'AccessKey' );
		$sConfirm = $oDP->post( 'AccessKeyConfirm' );

		$oResponse = new \FernleafSystems\Utilities\Response();

		$bSuccess = false;
		if ( empty( $sKey ) ) {
			$sMessage = 'Security access key was empty.';
		}
		else if ( $sKey != $sConfirm ) {
			$sMessage = 'Keys do not match.';
		}
		else {
			/** @var ICWP_WPSF_FeatureHandler_AdminAccessRestriction $oModule */
			$oModule = $this->getController()->getModule( 'admin_access_restriction' );
			try {
				$oModule->setNewAccessKeyManually( $sKey )
						->setPermissionToSubmit( true );
				$bSuccess = true;
				$sMessage = _wpsf__( 'Security Admin setup was successful.' );
			}
			catch ( Exception $oE ) {
				$sMessage = _wpsf__( $oE->getMessage() );
			}
		}

		return $oResponse->setSuccessful( $bSuccess )
						 ->setMessageText( $sMessage );
	}

	/**
	 * @return \FernleafSystems\Utilities\Response
	 */
	private function wizardAuditTrail() {
		$bEnabled = $this->loadDP()->post( 'AuditTrailOption' ) === 'Y';

		/** @var ICWP_WPSF_FeatureHandler_AdminAccessRestriction $oModule */
		$oModule = $this->getController()->getModule( 'audit_trail' );
		$oModule->setIsMainFeatureEnabled( $bEnabled )
				->savePluginOptions();

		$bSuccess = $oModule->getIsMainFeatureEnabled() === $bEnabled;
		if ( $bSuccess ) {
			$sMessage = sprintf( '%s has been %s.', _wpsf__( 'Audit Trail' ),
				$oModule->getIsMainFeatureEnabled() ? _wpsf__( 'Enabled' ) : _wpsf__( 'Disabled' )
			);
		}
		else {
			$sMessage = sprintf( _wpsf__( '%s setting could not be changed at this time.' ), _wpsf__( 'Audit Trail' ) );
		}

		$oResponse = new \FernleafSystems\Utilities\Response();
		return $oResponse->setSuccessful( $bSuccess )
						 ->setMessageText( $sMessage );
	}

	/**
	 * @return \FernleafSystems\Utilities\Response
	 */
	private function wizardIps() {

		$bEnabled = $this->loadDP()->post( 'IpManagerOption' ) === 'Y';

		/** @var ICWP_WPSF_FeatureHandler_Ips $oModule */
		$oModule = $this->getController()->getModule( 'ips' );
		$oModule->setIsMainFeatureEnabled( $bEnabled )
				->savePluginOptions();

		$bSuccess = $oModule->getIsMainFeatureEnabled() === $bEnabled;
		if ( $bSuccess ) {
			$sMessage = sprintf( '%s has been %s.', _wpsf__( 'IP Manager' ),
				$oModule->getIsMainFeatureEnabled() ? _wpsf__( 'Enabled' ) : _wpsf__( 'Disabled' )
			);
		}
		else {
			$sMessage = sprintf( _wpsf__( '%s setting could not be changed at this time.' ), _wpsf__( 'IP Manager' ) );
		}

		$oResponse = new \FernleafSystems\Utilities\Response();
		return $oResponse->setSuccessful( $bSuccess )
						 ->setMessageText( $sMessage );
	}

	/**
	 * @return \FernleafSystems\Utilities\Response
	 */
	private function wizardLoginProtect() {

		$bEnabled = $this->loadDP()->post( 'LoginProtectOption' ) === 'Y';

		/** @var ICWP_WPSF_FeatureHandler_LoginProtect $oModule */
		$oModule = $this->getController()->getModule( 'login_protect' );
		if ( $bEnabled ) { // we don't disable the whole module
			$oModule->setIsMainFeatureEnabled( true );
		}
		$oModule->setEnabledGaspCheck( $bEnabled )
				->savePluginOptions();

		$bSuccess = $oModule->getIsMainFeatureEnabled() === $bEnabled;
		if ( $bSuccess ) {
			$sMessage = sprintf( '%s has been %s.', _wpsf__( 'Login Protection' ),
				$oModule->getIsMainFeatureEnabled() ? _wpsf__( 'Enabled' ) : _wpsf__( 'Disabled' )
			);
		}
		else {
			$sMessage = sprintf( _wpsf__( '%s setting could not be changed at this time.' ), _wpsf__( 'Login Protection' ) );
		}

		$oResponse = new \FernleafSystems\Utilities\Response();
		return $oResponse->setSuccessful( $bSuccess )
						 ->setMessageText( $sMessage );
	}

	/**
	 * @return \FernleafSystems\Utilities\Response
	 */
	private function wizardOptin() {
		$oDP = $this->loadDP();

		$bEnabledTracking = $oDP->post( 'AnonymousOption', 'N', true ) === 'Y';
		$bEnabledBadge = $oDP->post( 'BadgeOption', 'N', true ) === 'Y';

		/** @var ICWP_WPSF_FeatureHandler_Plugin $oModule */
		$oModule = $this->getController()->getModule( 'plugin' );
		$oModule->setIsDisplayPluginBadge( $bEnabledBadge )
				->setPluginTrackingPermission( $bEnabledTracking );

		$sMessage = _wpsf__( 'Preferences have been saved.' );

		$oResponse = new \FernleafSystems\Utilities\Response();
		return $oResponse->setSuccessful( true )
						 ->setMessageText( $sMessage );
	}

	/**
	 * @return \FernleafSystems\Utilities\Response
	 */
	private function wizardCommentsFilter() {

		$bEnabled = $this->loadDP()->post( 'CommentsFilterOption' ) === 'Y';

		/** @var ICWP_WPSF_FeatureHandler_CommentsFilter $oModule */
		$oModule = $this->getController()->getModule( 'comments_filter' );
		if ( $bEnabled ) { // we don't disable the whole module
			$oModule->setIsMainFeatureEnabled( true );
		}
		$oModule->setEnabledGasp( $bEnabled )
				->savePluginOptions();

		$bSuccess = $oModule->getIsMainFeatureEnabled() === $bEnabled;
		if ( $bSuccess ) {
			$sMessage = sprintf( '%s has been %s.', _wpsf__( 'Comment SPAM Protection' ),
				$oModule->getIsMainFeatureEnabled() ? _wpsf__( 'Enabled' ) : _wpsf__( 'Disabled' )
			);
		}
		else {
			$sMessage = sprintf( _wpsf__( '%s setting could not be changed at this time.' ), _wpsf__( 'Comment SPAM Protection' ) );
		}

		$oResponse = new \FernleafSystems\Utilities\Response();
		return $oResponse->setSuccessful( $bSuccess )
						 ->setMessageText( $sMessage );
	}

	/**
	 * @return string
	 */
	public function getCurrentWizard() {
		return $this->sCurrentWizard;
	}

	/**
	 * @param string $sCurrentWizard
	 * @return $this
	 */
	public function setCurrentWizard( $sCurrentWizard ) {
		$this->sCurrentWizard = $sCurrentWizard;
		return $this;
	}
}