//<?php
/**
 * @brief		Hook on \IPS\core\modules\admin\support\errorLogs
 * @author		XENNTEC UG
 * @copyright	(c) 2026 XENNTEC UG
 * @package		X Log Cleaner
 * @since		1.0.0
 */

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !\defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class hook476 extends _HOOK_CLASS_
{
	public static function hookData()
	{
		return array();
	}

	/**
	 * Override manage() to inject "Delete Error Logs" sidebar button
	 *
	 * @return	void
	 */
	protected function manage()
	{
		try
		{
			parent::manage();

			$hasLogs = \IPS\Db::i()->select( 'COUNT(*)', 'core_error_logs' )->first();

			\IPS\Output::i()->sidebar['actions']['xlcDeleteErrorLogs'] = array(
				'title' => 'xlc_delete_error_logs',
				'icon'  => 'trash',
			);

			if ( $hasLogs )
			{
				\IPS\Output::i()->sidebar['actions']['xlcDeleteErrorLogs']['link'] = \IPS\Http\Url::internal( 'app=core&module=support&controller=errorLogs&do=xlcDeleteErrorLogs' )->csrf();
				\IPS\Output::i()->sidebar['actions']['xlcDeleteErrorLogs']['data'] = array( 'ipsDialog' => '', 'ipsDialog-title' => \IPS\Member::loggedIn()->language()->addToStack( 'xlc_delete_error_logs' ) );
			}
			else
			{
				\IPS\Output::i()->sidebar['actions']['xlcDeleteErrorLogs']['class'] = 'ipsButton_disabled';
			}
		}
		catch ( \Error | \RuntimeException $e )
		{
			if ( method_exists( get_parent_class(), __FUNCTION__ ) )
			{
				return \call_user_func_array( 'parent::' . __FUNCTION__, \func_get_args() );
			}
			else
			{
				throw $e;
			}
		}
	}

	/**
	 * Delete error logs with a "delete all" toggle and error level multi-select
	 *
	 * @return	void
	 */
	protected function xlcDeleteErrorLogs()
	{
		try
		{
			\IPS\Session::i()->csrfCheck();

			$form = new \IPS\Helpers\Form;

			$form->add( new \IPS\Helpers\Form\YesNo( 'xlc_delete_all_errors_toggle', FALSE, FALSE, array(
				'togglesOff' => array( 'xlc_delete_or_levels', 'xlc_error_levels' ),
			) ) );

			$form->addDummy( '', \IPS\Member::loggedIn()->language()->addToStack( 'xlc_delete_or_levels' ), NULL, NULL, 'xlc_delete_or_levels' );

			/* Build level options from distinct first-digit of log_error_code */
			$levelOptions = array();
			$levelLabels  = array(
				'1' => \IPS\Member::loggedIn()->language()->addToStack( 'xlc_error_level_1' ),
				'2' => \IPS\Member::loggedIn()->language()->addToStack( 'xlc_error_level_2' ),
				'3' => \IPS\Member::loggedIn()->language()->addToStack( 'xlc_error_level_3' ),
				'4' => \IPS\Member::loggedIn()->language()->addToStack( 'xlc_error_level_4' ),
				'5' => \IPS\Member::loggedIn()->language()->addToStack( 'xlc_error_level_5' ),
			);

			try
			{
				$distinctLevels = \IPS\Db::i()->select( 'DISTINCT(SUBSTRING(log_error_code, 1, 1)) AS lvl', 'core_error_logs' );
				foreach ( $distinctLevels as $row )
				{
					$lvl = $row;
					if ( isset( $levelLabels[ $lvl ] ) )
					{
						$levelOptions[ $lvl ] = $levelLabels[ $lvl ];
					}
				}
			}
			catch ( \Exception $e )
			{
				/* Fallback: offer all levels */
				$levelOptions = $levelLabels;
			}

			ksort( $levelOptions );

			$form->add( new \IPS\Helpers\Form\CheckboxSet( 'xlc_error_levels', NULL, FALSE, array(
				'options' => $levelOptions,
			), NULL, NULL, NULL, 'xlc_error_levels' ) );

			$form->add( new \IPS\Helpers\Form\Checkbox( 'xlc_confirm_delete', FALSE, TRUE, array(), function( $val ) {
				if ( empty( $val ) )
				{
					throw new \DomainException( 'xlc_must_confirm_delete' );
				}
			} ) );

			if ( $values = $form->values() )
			{
				$deleted = FALSE;

				if ( $values['xlc_delete_all_errors_toggle'] )
				{
					\IPS\Db::i()->delete( 'core_error_logs' );
					\IPS\Session::i()->log( 'xlc_acplog__all_error_logs' );
					$deleted = TRUE;
				}
				elseif ( !empty( $values['xlc_error_levels'] ) )
				{
					$conditions = array();
					$binds = array();
					foreach ( $values['xlc_error_levels'] as $level )
					{
						$conditions[] = 'log_error_code LIKE ?';
						$binds[] = $level . '%';
					}
					\IPS\Db::i()->delete( 'core_error_logs', \array_merge( array( implode( ' OR ', $conditions ) ), $binds ) );
					\IPS\Session::i()->log( 'xlc_acplog__error_levels', array( implode( ', ', $values['xlc_error_levels'] ) => FALSE ) );
					$deleted = TRUE;
				}

				$redirectUrl = \IPS\Http\Url::internal( 'app=core&module=support&controller=errorLogs' );
				if ( $deleted )
				{
					\IPS\Output::i()->redirect( $redirectUrl, 'deleted' );
				}

				\IPS\Output::i()->redirect( $redirectUrl );
			}

			\IPS\Output::i()->title  = \IPS\Member::loggedIn()->language()->addToStack( 'xlc_delete_error_logs' );
			\IPS\Output::i()->output = $form;
		}
		catch ( \Error | \RuntimeException $e )
		{
			if ( method_exists( get_parent_class(), __FUNCTION__ ) )
			{
				return \call_user_func_array( 'parent::' . __FUNCTION__, \func_get_args() );
			}
			else
			{
				throw $e;
			}
		}
	}
}
