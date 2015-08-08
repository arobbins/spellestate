<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'RWMB_Section_Field' ) )
{
	class RWMB_Section_Field extends RWMB_Field
	{
		/**
		 * Get field HTML
		 *
		 * @param string $html
		 * @param mixed  $meta
		 * @param array  $field
		 *
		 * @return string
		 */
		static function html( $meta, $field )
		{
			return sprintf(
				'<h2 id="%s" class="meta-box-section">%s</h2>',
				$field['id'],
				$field['title']
			);
		}

	}
}