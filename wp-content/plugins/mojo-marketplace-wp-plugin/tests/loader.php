<?php
function mm_jpo_test( $file ) {
	return mm_ab_test_file( 'jetpack-onboarding-v1.7.1', $file, 'vendor/jetpack/jetpack-onboarding/jetpack-onboarding.php', 'tests/jetpack-onboarding/jetpack-onboarding.php', 25, DAY_IN_SECONDS * 90 );
}
add_filter( 'mm_require_file', 'mm_jpo_test' );

function mm_jpo_test_exempt() {
	mm_ab_test_inclusion( 'jetpack-onboarding-v1.7.1-exempt', md5( 'jetpack-onboarding-v1.7.1-exempt' ), 20, DAY_IN_SECONDS * 30 );
}
add_action( 'init', 'mm_jpo_test_exempt', 7 );

