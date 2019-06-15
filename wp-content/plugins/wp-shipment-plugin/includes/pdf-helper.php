<?php

if ( ! class_exists( 'WPSP_PdfHelper' ) ) {

	class WPSP_PdfHelper
	{
		const DESTINATION__INLINE = "I";
		const DESTINATION__DOWNLOAD = "D";
		const DESTINATION__DISK = "F";
		const DESTINATION__DISK_INLINE = "FI";
		const DESTINATION__DISK_DOWNLOAD = "FD";
		const DESTINATION__BASE64_RFC2045 = "E";

		const DEFAULT_DESTINATION = self::DESTINATION__INLINE;
		const DEFAULT_MERGED_FILE_NAME = __DIR__ . "/merged-files.pdf";

		public static function generate( $data, $filepath, $subject = '', $subtitle = '', $data_type = 'text' )
		{
			$orientation = PDF_PAGE_ORIENTATION;

			if ( $data_type == "image" ) {
				$orientation = "L";
			}

			$pdf = new TCPDF( $orientation, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );

			// set document information
			$pdf->SetCreator( PDF_CREATOR );
			$pdf->SetTitle( date( "Y-m-d-h-i-s" ) );
			$pdf->SetSubject( $subject );

			if ( $subtitle ) {
				$subtitle .= ' - ';
			}

			// set default header data
			$pdf->SetHeaderData( null, 0, get_bloginfo( 'name' ), $subtitle . $_SERVER['SERVER_NAME'], array(
				0,
				64,
				255
			), array( 0, 64, 128 ) );
			$pdf->setFooterData( array( 0, 64, 0 ), array( 0, 64, 128 ) );

			// set header and footer fonts
			$pdf->setHeaderFont( Array( PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN ) );
			$pdf->setFooterFont( Array( PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA ) );

			// set default monospaced font
			$pdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED );

			// set margins
			$pdf->SetMargins( PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT );
			$pdf->SetHeaderMargin( PDF_MARGIN_HEADER );
			$pdf->SetFooterMargin( PDF_MARGIN_FOOTER );

			// set auto page breaks
			$pdf->SetAutoPageBreak( true, PDF_MARGIN_BOTTOM );

			// Add a page
			// This method has several options, check the source code documentation for more information.
			$pdf->AddPage();

			// Set some content to print
			switch ( $data_type ) {
				case "text":
					$html = <<<EOD
					{$data}
EOD;
					// Print text using writeHTMLCell()
					$pdf->writeHTML( $html );
					break;
				case "image":
					$pdf->Image( $data );
					break;
			}

			$pdf->Output( $filepath, 'F' );
		}

		public static function merge( $files, $destination = null, $outputPath = null )
		{
			if ( empty( $destination ) ) {
				$destination = self::DEFAULT_DESTINATION;
			}

			if ( empty( $outputPath ) ) {
				$outputPath = self::DEFAULT_MERGED_FILE_NAME;
			}

			$pdf = new FPDI();
			$pdf->setPrintHeader( false );
			$pdf->setPrintFooter( false );

			self::join( $pdf, $files );

			$pdf->Output( $outputPath, $destination );
		}

		private static function join( $pdf, $fileList )
		{
			if ( empty( $fileList ) || ! is_array( $fileList ) ) {
				die( "invalid file list" );
			}

			foreach ( $fileList as $file ) {
				self::addFile( $pdf, $file );
			}
		}

		private static function addFile( $pdf, $file )
		{
			$numPages = $pdf->setSourceFile( $file );

			if ( empty( $numPages ) || $numPages < 1 ) {
				return;
			}

			for ( $x = 1; $x <= $numPages; $x ++ ) {
				$pdf->AddPage();
				$pdf->useTemplate( $pdf->importPage( $x ), null, null, 0, 0, true );
				$pdf->endPage();
			}
		}
	}
}