<?php

function extract_documents(String $ead) {
	$xh = xslt_create(EAD_IMPORT_DOC_EXTRACTOR);
	return xslt_process($xh, $ead);
}

function extract_persons(String $ead) {
	$xh = xslt_create(EAD_IMPORT_PERSONS_EXTRACTOR);
	return xslt_process($xh, $ead);
}

?>