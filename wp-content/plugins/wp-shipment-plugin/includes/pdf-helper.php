<?php

if ( ! class_exists( 'WPSP_PdfHelper' ) ) {

	class WPSP_PdfHelper
	{

		public static function generate( $data, $filename, $subject = '', $subtitle = '', $data_type = 'text' )
		{
			$filepath = WPSP_FILES_DIR . $filename;
			$fileurl  = WPSP_FILES_URL . $filename;

			if ( ! file_exists( WPSP_FILES_DIR ) ) {
				mkdir( WPSP_FILES_DIR, 0775 );
			}

			$a = file_put_contents( $filepath, ( $data ) );
			var_dump( $data );
			die;

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

			return array(
				"path" => $filepath,
				"url"  => $fileurl
			);
		}
	}
}