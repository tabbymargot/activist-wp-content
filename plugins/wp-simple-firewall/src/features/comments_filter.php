<?php

if ( class_exists( 'ICWP_WPSF_FeatureHandler_CommentsFilter', false ) ) {
	return;
}

require_once( dirname( __FILE__ ).DIRECTORY_SEPARATOR.'base_wpsf.php' );

class ICWP_WPSF_FeatureHandler_CommentsFilter extends ICWP_WPSF_FeatureHandler_BaseWpsf {

	/**
	 * @return bool
	 */
	protected function isReadyToExecute() {
		return parent::isReadyToExecute() && !$this->isVisitorWhitelisted();
	}

	/**
	 * @return boolean
	 */
	public function getIfDoCommentsCheck() {
		return apply_filters( $this->prefix( 'if-do-comments-check' ), true );
	}

	/**
	 * @return boolean
	 */
	public function getIfCheckCommentToken() {
		return ( $this->getOpt( 'comments_token_expire_interval' ) > 0 || $this->getOpt( 'comments_cooldown_interval' ) > 0 );
	}

	/**
	 * @return string
	 */
	public function getGoogleRecaptchaStyle() {
		$sStyle = $this->getOpt( 'google_recaptcha_style_comments' );
		if ( $sStyle == 'default' ) {
			$sStyle = parent::getGoogleRecaptchaStyle();
		}
		return $sStyle;
	}

	/**
	 * @param string $sOptKey
	 * @return string
	 */
	public function getTextOptDefault( $sOptKey ) {

		switch ( $sOptKey ) {
			case 'custom_message_checkbox':
				$sText = _wpsf__( "I'm not a spammer." );
				break;
			case 'custom_message_alert':
				$sText = _wpsf__( "Please check the box to confirm you're not a spammer." );
				break;
			case 'custom_message_comment_wait':
				$sText = _wpsf__( "Please wait %s seconds before posting your comment." );
				break;
			case 'custom_message_comment_reload':
				$sText = _wpsf__( "Please reload this page to post a comment." );
				break;
			default:
				$sText = parent::getTextOptDefault( $sOptKey );
				break;
		}
		return $sText;
	}

	protected function doExtraSubmitProcessing() {

		if ( $this->getOpt( 'comments_cooldown_interval' ) < 0 ) {
			$this->getOptionsVo()->resetOptToDefault( 'comments_cooldown_interval' );
		}

		if ( $this->getOpt( 'comments_token_expire_interval' ) < 0 ) {
			$this->getOptionsVo()->resetOptToDefault( 'comments_token_expire_interval' );
		}

		if ( $this->getOpt( 'comments_token_expire_interval' ) != 0 && $this->getOpt( 'comments_cooldown_interval' ) > $this->getOpt( 'comments_token_expire_interval' ) ) {
			$this->getOptionsVo()->resetOptToDefault( 'comments_cooldown_interval' );
			$this->getOptionsVo()->resetOptToDefault( 'comments_token_expire_interval' );
		}

		$aCommentsFilters = $this->getOpt( 'enable_comments_human_spam_filter_items' );
		if ( empty( $aCommentsFilters ) || !is_array( $aCommentsFilters ) ) {
			$this->getOptionsVo()->resetOptToDefault( 'enable_comments_human_spam_filter_items' );
		}
	}

	/**
	 * This is the point where you would want to do any options verification
	 */
	protected function doPrePluginOptionsSave() {
		// TODO: remove as it's a temporary transition for clashing options
		if ( $this->getOptIs( 'enable_google_recaptcha', 'Y' ) ) {
			$this->setOpt( 'enable_google_recaptcha_comments', 'Y' );
		}
	}

	/**
	 * @param array $aOptionsParams
	 * @return array
	 * @throws Exception
	 */
	protected function loadStrings_SectionTitles( $aOptionsParams ) {

		switch ( $aOptionsParams[ 'slug' ] ) {

			case 'section_enable_plugin_feature_spam_comments_protection_filter' :
				$sTitle = sprintf( _wpsf__( 'Enable Plugin Feature: %s' ), _wpsf__( 'SPAM Comments Protection Filter' ) );
				$aSummary = array(
					sprintf( _wpsf__( 'Purpose - %s' ), _wpsf__( 'The Comments Filter can block 100% of automated spam bots and also offer the option to analyse human-generated spam.' ) ),
					sprintf( _wpsf__( 'Recommendation - %s' ), sprintf( _wpsf__( 'Keep the %s feature turned on.' ), _wpsf__( 'Comments Filter' ) ) )
				);
				$sTitleShort = sprintf( '%s / %s', _wpsf__( 'Enable' ), _wpsf__( 'Disable' ) );
				break;

			case 'section_bot_comment_spam_protection_filter' :
				$sTitle = sprintf( _wpsf__( '%s Comment SPAM Protection Filter' ), _wpsf__( 'Automatic Bot' ) );
				$aSummary = array(
					sprintf( _wpsf__( 'Purpose - %s' ), _wpsf__( 'Blocks 100% of all automated bot-generated comment SPAM.' ) ),
					sprintf( _wpsf__( 'Recommendation - %s' ), _wpsf__( 'Use of this feature is highly recommend.' ) )
				);
				$sTitleShort = _wpsf__( 'Bot SPAM' );
				break;

			case 'section_recaptcha' :
				$sTitle = 'Google reCAPTCHA';
				$sTitleShort = 'reCAPTCHA';
				$aSummary = array(
					sprintf( _wpsf__( 'Purpose - %s' ), _wpsf__( 'Adds Google reCAPTCHA to the Comment Forms.' ) ),
					sprintf( _wpsf__( 'Recommendation - %s' ), _wpsf__( 'Keep this turned on.' ) ),
					sprintf( _wpsf__( 'Note - %s' ), _wpsf__( "You will need to register for Google reCAPTCHA keys and store them in the Shield 'Dashboard' settings." ) ),
				);
				break;

			case 'section_human_spam_filter' :
				$sTitle = sprintf( _wpsf__( '%s Comment SPAM Protection Filter' ), _wpsf__( 'Human' ) );
				$aSummary = array(
					sprintf( _wpsf__( 'Purpose - %s' ), _wpsf__( 'Uses a 3rd party SPAM dictionary to detect human-based comment SPAM.' ) ),
					sprintf( _wpsf__( 'Recommendation - %s' ), _wpsf__( 'Use of this feature is highly recommend.' ) ),
					_wpsf__( 'This tool, unlike other SPAM tools such as Akismet, will not send your comment data to 3rd party services for analysis.' )
				);
				$sTitleShort = _wpsf__( 'Human SPAM' );
				break;

			default:
				list( $sTitle, $sTitleShort, $aSummary ) = $this->loadStrings_SectionTitlesDefaults( $aOptionsParams );
		}
		$aOptionsParams[ 'title' ] = $sTitle;
		$aOptionsParams[ 'summary' ] = ( isset( $aSummary ) && is_array( $aSummary ) ) ? $aSummary : array();
		$aOptionsParams[ 'title_short' ] = $sTitleShort;
		return $aOptionsParams;
	}

	/**
	 * @param array $aOptionsParams
	 * @return array
	 * @throws Exception
	 */
	protected function loadStrings_Options( $aOptionsParams ) {

		$sKey = $aOptionsParams[ 'key' ];
		switch ( $sKey ) {

			case 'enable_comments_filter' :
				$sName = sprintf( _wpsf__( 'Enable %s' ), $this->getMainFeatureName() );
				$sSummary = _wpsf__( 'Enable (or Disable) The SPAM Comments Protection Filter Feature' );
				$sDescription = sprintf( _wpsf__( 'Checking/Un-Checking this option will completely turn on/off the whole %s feature.' ), _wpsf__( 'SPAM Comments Protection Filter' ) );
				break;

			case 'enable_comments_human_spam_filter' :
				$sName = _wpsf__( 'Human SPAM Filter' );
				$sSummary = sprintf( _wpsf__( 'Enable (or Disable) The %s Feature' ), _wpsf__( 'Human SPAM Filter' ) );
				$sDescription = _wpsf__( 'Scans the content of WordPress comments for keywords that are indicative of SPAM and marks the comment according to your preferred setting below.' );
				break;

			case 'enable_comments_human_spam_filter_items' :
				$sName = _wpsf__( 'Comment Filter Items' );
				$sSummary = _wpsf__( 'Select The Items To Scan For SPAM' );
				$sDescription = _wpsf__( 'When a user submits a comment, only the selected parts of the comment data will be scanned for SPAM content.' ).' '.sprintf( _wpsf__( 'Recommended: %s' ), _wpsf__( 'All' ) );
				break;

			case 'comments_default_action_human_spam' :
				$sName = _wpsf__( 'Default SPAM Action' );
				$sSummary = _wpsf__( 'How To Categorise Comments When Identified To Be SPAM' );
				$sDescription = sprintf( _wpsf__( 'When a comment is detected as being SPAM from %s, the comment will be categorised based on this setting.' ), '<span style"text-decoration:underline;">'._wpsf__( 'a human commenter' ).'</span>' );
				break;

			case 'enable_comments_gasp_protection' :
				$sName = _wpsf__( 'GASP Protection' );
				$sSummary = _wpsf__( 'Add Growmap Anti Spambot Protection to your comments' );
				$sDescription = _wpsf__( 'Taking the lead from the original GASP plugin for WordPress, we have extended it to include advanced spam-bot protection.' );
				break;

			case 'comments_default_action_spam_bot' :
				$sName = _wpsf__( 'Default SPAM Action' );
				$sSummary = _wpsf__( 'How To Categorise Comments When Identified To Be SPAM' );
				$sDescription = sprintf( _wpsf__( 'When a comment is detected as being SPAM from %s, the comment will be categorised based on this setting.' ), '<span style"text-decoration:underline;">'._wpsf__( 'an automatic bot' ).'</span>' );
				break;

			case 'comments_cooldown_interval' :
				$sName = _wpsf__( 'Comments Cooldown' );
				$sSummary = _wpsf__( 'Limit posting comments to X seconds after the page has loaded' );
				$sDescription = _wpsf__( "By forcing a comments cooldown period, you restrict a Spambot's ability to post multiple times to your posts." );
				break;

			case 'comments_token_expire_interval' :
				$sName = _wpsf__( 'Comment Token Expire' );
				$sSummary = _wpsf__( 'A visitor has X seconds within which to post a comment' );
				$sDescription = _wpsf__( "Default: 600 seconds (10 minutes). Each visitor is given a unique 'Token' so they can comment. This restricts spambots, but we need to force these tokens to expire and at the same time not bother the visitors." );
				break;

			case 'custom_message_checkbox' :
				$sName = _wpsf__( 'GASP Checkbox Message' );
				$sSummary = _wpsf__( 'If you want a custom checkbox message, please provide this here' );
				$sDescription = _wpsf__( "You can customise the message beside the checkbox." )
								.'<br />'.sprintf( _wpsf__( 'Default Message: %s' ), _wpsf__( "Please check the box to confirm you're not a spammer" ) );
				break;

			case 'enable_google_recaptcha_comments' :
				$sName = 'Google reCAPTCHA';
				$sSummary = _wpsf__( 'Enable Google reCAPTCHA For Comments' );
				$sDescription = _wpsf__( 'Use Google reCAPTCHA on the comments form to prevent bot-spam comments.' );
				break;

			case 'google_recaptcha_style_comments' :
				$sName = _wpsf__( 'reCAPTCHA Style' );
				$sSummary = _wpsf__( 'How Google reCAPTCHA Will Be Displayed' );
				$sDescription = _wpsf__( 'You can choose the reCAPTCHA display format that best suits your site, including the new Invisible Recaptcha' );
				break;

			case 'custom_message_alert' :
				$sName = _wpsf__( 'GASP Alert Message' );
				$sSummary = _wpsf__( 'If you want a custom alert message, please provide this here' );
				$sDescription = _wpsf__( "This alert message is displayed when a visitor attempts to submit a comment without checking the box." )
								.'<br />'.sprintf( _wpsf__( 'Default Message: %s' ), _wpsf__( "Please check the box to confirm you're not a spammer" ) );
				break;

			case 'custom_message_comment_wait' :
				$sName = _wpsf__( 'GASP Wait Message' );
				$sSummary = _wpsf__( 'If you want a custom submit-button wait message, please provide this here.' );
				$sDescription = _wpsf__( "Where you see the '%s' this will be the number of seconds. You must ensure you include 1, and only 1, of these." )
								.'<br />'.sprintf( _wpsf__( 'Default Message: %s' ), _wpsf__( 'Please wait %s seconds before posting your comment' ) );
				break;

			case 'custom_message_comment_reload' :
				$sName = _wpsf__( 'GASP Reload Message' );
				$sSummary = _wpsf__( 'If you want a custom message when the comment token has expired, please provide this here.' );
				$sDescription = _wpsf__( 'This message is displayed on the submit-button when the comment token is expired' )
								.'<br />'.sprintf( _wpsf__( 'Default Message: %s' ), _wpsf__( "Please reload this page to post a comment" ) );
				break;

			default:
				throw new Exception( sprintf( 'An option has been defined but without strings assigned to it. Option key: "%s".', $sKey ) );
		}

		$aOptionsParams[ 'name' ] = $sName;
		$aOptionsParams[ 'summary' ] = $sSummary;
		$aOptionsParams[ 'description' ] = $sDescription;
		return $aOptionsParams;
	}

	/**
	 * @return bool
	 */
	public function getIsGoogleRecaptchaEnabled() {
		return ( $this->getOptIs( 'enable_google_recaptcha_comments', 'Y' ) && $this->getIsGoogleRecaptchaReady() );
	}

	/**
	 * @return string
	 */
	public function getCommentsFilterTableName() {
		return $this->prefix( $this->getDefinition( 'spambot_comments_filter_table_name' ), '_' );
	}
}