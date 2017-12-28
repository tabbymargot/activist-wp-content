<?php
/*
Plugin Name: GM Block Bots
Plugin URI: http://www.greenmellenmedia.com/plugins/gm-block-bots/
Description: Blocks traffic from SEMalt, buttons-for-website and others
Version: 2.0.2
Author: GreenMellen Media
Author URI: http://www.greenmellenmedia.com/
License: GPLv2 or later
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function gm_block_start() {
	$wp_bs_loaded = new GM_Block_Bots();
}

add_action( 'plugins_loaded', 'gm_block_start' );

class GM_Block_Bots {

	public function __construct() {
		add_action( 'parse_request', array( $this, 'block_bots' ) );
	}

	public function block_bots() {
		$referer = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : false;

		if ( empty( $referer ) ) {
			return;
		}
		
		$referer = strtolower($referer);
		
		$bot_array = array('semalt.com', 'buttons-for-website.com', 'darodar.com', 'social-buttons', '7makemoneyonline.com', 'ilovevitaly', 'simple-share-buttons.com', 'clicksor.com', 'bestwebsitesawards.com', 'aliexpress.com', 'savetubevideo.com', 'kambasoft.com', 'priceg.com', 'blackhatworth.com', 'hulfingtonpost.com', 'econom.co', 'ranksonic.org', 'ranksonic.info', '4webmasters.org', 'anticrawler.org', 'bestsub.com', 'o-o-6-o-o.com', 'search.tb.ask.com', 'wow.com', 'adviceforum.info', 'makemoneyonline.com', 'best-seo-solution.com', 'get-free-traffic-now.com', 'buy-cheap-online.info', 'best-seo-offer.com', 'buttons-for-your-website.com', 'googlsucks.com', 'theguardlan.com', 'torture.ml', 'hol.es', 'domination.ml', 'free-share-buttons.com', 'uni.me', 'search.myway.com', 'guardlink.com', 'event-tracking.com', 'free-social-buttons.com', 'kabbalah-red-bracelets.com', 'guardlink.org', 'sanjosestartups.com', '100dollars-seo.com', 'howtostopreferralspam.eu', 'floating-share-buttons.com', 'videos-for-your-business.com', 'success-seo.com', 'webmonetizer.net', 'trafficmonetizer.net', 'e-buyeasy.com', 'traffic2money.com', 'sexyali.com', 'get-free-social-traffic.com', 'chinese-amezon.com', 'erot.co', 'hongfanji.com', 'video--production.com', 'rankscanner.com', 'yourserverisdown.com', 'free-floating-buttons.com', 'how-to-earn-quick-money.com', 'qualitymarketzone.com', 'seo-platform.com', 'rankings-analytics.com', 'copyrightclaims.org', 'snip.to', 'amazonaws.com', 'top1-seo-service.com', 'site-16528012-1.snip.tw', 'website-analyzer.info', 'rank-checker.online', 'keywords-monitoring-your-success.com', 'free-video-tool.com', 'social-traffic-', 'uptime.com', 'monetizationking.net', 'law-enforcement-bot', 'cookie-law-enforcement', 'fix-website-errors.com', 'pizza-tycoon.com', 'hvd-store.com', 'burger-imperia.com', 'ebin.cc', 'site-auditor.online', 'social-s', 'flummox.ml', 'magicdiet.gq', 'pogodnyyeavarii.gq', 'free-share-buttons.top', 'cpty.com', 'ɢoogle.com', 'lifehacĸer.com', '.xyz/', '.ru/', '.ua/', '.cf/', '.ga/', '.ml/', '.re/', '.tk/', '.kz/', '.ro/', '.pl/', '0n-line.tv', 
'1-99seo.com', 
'1-free-share-buttons.com', 
'12masterov.com', 
'24x7-server-support.site', 
'2your.site', 
'3-letter-domains.net', 
'6hopping.com', 
'7zap.com', 
'acads.net', 
'acunetix-referrer.com', 
'adcash.com', 
'adspart.com', 
'adventureparkcostarica.com', 
'adviceforum.info', 
'affordablewebsitesandmobileapps.com', 
'akuhni.by', 
'alibestsale.com', 
'allknow.info', 
'allnews.md', 
'allwomen.info', 
'alpharma.net', 
'android-style.com', 
'anticrawler.org', 
'arkkivoltti.net', 
'aruplighting.com', 
'autovideobroadcast.com', 
'aviva-limoux.com', 
'azartclub.org', 
'azlex.uz', 
'baixar-musicas-gratis.com', 
'balitouroffice.com', 
'best-seo-offer.com', 
'bestmobilityscooterstoday.com', 
'bestwebsitesawards.com', 
'bif-ru.info', 
'biglistofwebsites.com', 
'bizru.info', 
'blogtotal.de', 
'blue-square.biz', 
'bluerobot.info', 
'boostmyppc.com', 
'brakehawk.com', 
'break-the-chains.com', 
'burger-imperia.com', 
'buy-cheap-pills-order-online.com', 
'call-of-duty.info', 
'chcu.net', 
'cityadspix.com', 
'civilwartheater.com', 
'clicksor.com', 
'coderstate.com', 
'codysbbq.com', 
'compliance-john.top', 
'compliance-julianna.top', 
'conciergegroup.org', 
'connectikastudio.com', 
'covadhosting.biz', 
'cubook.supernew.org', 
'dailyrank.net', 
'datract.com', 
'dbutton.net', 
'demenageur.com', 
'descargar-musica-gratis.net', 
'dipstar.org', 
'dogsrun.net', 
'dojki-hd.com', 
'domain-tracker.com', 
'dostavka-v-krym.com', 
'drupa.com', 
'egovaleo.it', 
'ekto.ee', 
'elmifarhangi.com', 
'escort-russian.com', 
'fast-wordpress-start.com', 
'fbdownloader.com', 
'for-your.website', 
'forsex.info', 
'forum69.info', 
'freenode.info', 
'freewhatsappload.com', 
'fsalas.com', 
'generalporn.org', 
'germes-trans.com', 
'get-your-social-buttons.info', 
'getrichquickly.info', 
'ghostvisitor.com', 
'gobongo.info', 
'googlemare.com', 
'handicapvantoday.com', 
'havepussy.com', 
'hdmoviecamera.net', 
'hdmoviecams.com', 
'hosting-tracker.com', 
'howtostopreferralspam.eu', 
'humanorightswatch.org', 
'hundejo.com', 
'igadgetsworld.com', 
'igru-xbox.net', 
'ilikevitaly.com', 
'iminent.com', 
'increasewwwtraffic.info', 
'kazrent.com', 
'keywords-monitoring-success.com', 
'kino-key.info', 
'kinopolet.net', 
'knigonosha.net', 
'konkursov.net', 
'laxdrills.com', 
'legalrc.biz', 
'livefixer.com', 
'lumb.co', 
'masterseek.com', 
'meds-online24.com', 
'minegam.com', 
'mirtorrent.net', 
'mobilemedia.md', 
'myftpupload.com', 
'myplaycity.com', 
'nufaq.com', 
'o-o-11-o-o.com', 
'o-o-8-o-o.com', 
'online-hit.info', 
'online-templatestore.com', 
'onlinetvseries.me', 
'onlywoman.org', 
'ozas.net', 
'petrovka-online.com', 
'photokitchendesign.com', 
'pizza-imperia.com', 
'popads.net', 
'pops.foundation', 
'pornhub-forum.uni.me', 
'pornhub-ru.com', 
'pornoforadult.com', 
'pornogig.com', 
'pricheski-video.com', 
'pron.pro', 
'ranksonic.net', 
'rednise.com', 
'resellerclub.com', 
'responsive-test.net', 
'reversing.cc', 
'rightenergysolutions.com.au', 
'rumamba.com', 
'savetubevideo.info', 
'screentoolkit.com', 
'scripted.com', 
'search-error.com', 
'semaltmedia.com', 
'seo-2-0.com', 
'seoanalyses.com', 
'seopub.net', 
'sexsaoy.com', 
'sharebutton.net', 
'sharebutton.to', 
'shop.xz618.com', 
'siteripz.net', 
'sitevaluation.org', 
'sledstvie-veli.net', 
'smailik.org', 
'smartphonediscount.info', 
'snip.tw', 
'socialtrade.biz', 
'sohoindia.net', 
'sosdepotdebilan.com', 
'speedup-my.site', 
'superiends.org', 
'tattooha.com', 
'tedxrj.com', 
'theguardlan.com', 
'tomck.com', 
'topseoservices.co', 
'traffic2cash.org', 
'trafficmonetize.org', 
'uptimechecker.com', 
'uzungil.com', 
'vesnatehno.com', 
'video-woman.com', 
'viel.su', 
'vodaodessa.com', 
'w3javascript.com', 
'wallpaperdesk.info', 
'webmaster-traffic.com', 
'website-analytics.online', 
'website-speed-check.site', 
'website-speed-checker.site', 
'websites-reviews.com', 
'websocial.me', 
'wmasterlead.com', 
'wordpress-crew.net', 
'wordpresscore.com', 
'youporn-forum.uni.me', 
'youporn-ru.com', 
'zastroyka.org', 
'zoominfo.com');
		
		foreach($bot_array as $bots) {
			if ( strpos( $referer, $bots ) !== false ) {
				wp_die( '', '', array( 'response' => 403 ) );
				exit;
			}
		}
	}
}
