//<?php
/**
 * @brief		Hook on \IPS\core\modules\admin\support\systemLogs
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

class hook193 extends _HOOK_CLASS_
{
	/**
	 * Override manage() to inject "Delete All Logs" sidebar button
	 *
	 * @return	void
	 */
	protected function manage()
	{
		try
		{
			parent::manage();

			$hasLogs = \IPS\Db::i()->select( 'COUNT(*)', 'core_log' )->first();

			\IPS\Output::i()->sidebar['actions']['xlcDeleteSystemLogs'] = array(
				'title' => 'xlc_delete_system_logs',
				'icon'  => 'trash',
			);

			if ( $hasLogs )
			{
				\IPS\Output::i()->sidebar['actions']['xlcDeleteSystemLogs']['link'] = \IPS\Http\Url::internal( 'app=core&module=support&controller=systemLogs&do=xlcDeleteSystemLogs' );
				\IPS\Output::i()->sidebar['actions']['xlcDeleteSystemLogs']['data'] = array( 'ipsDialog' => '', 'ipsDialog-title' => \IPS\Member::loggedIn()->language()->addToStack( 'xlc_delete_system_logs' ) );
			}
			else
			{
				\IPS\Output::i()->sidebar['actions']['xlcDeleteSystemLogs']['class'] = 'ipsButton_disabled';
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
	 * Override fileLogs() to inject "Delete File Logs" sidebar button
	 *
	 * @return	void
	 */
	protected function fileLogs()
	{
		try
		{
			\IPS\Output::i()->sidebar['actions']['xlcDeleteFileLogs'] = array(
				'title' => 'xlc_delete_file_logs',
				'icon'  => 'trash',
				'link'  => \IPS\Http\Url::internal( 'app=core&module=support&controller=systemLogs&do=xlcDeleteFileLogs' ),
				'data'  => array( 'ipsDialog' => '', 'ipsDialog-title' => \IPS\Member::loggedIn()->language()->addToStack( 'xlc_delete_file_logs' ) ),
			);

			parent::fileLogs();
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
	 * Delete system logs — form with "delete all" toggle and category multi-select
	 *
	 * @return	void
	 */
	protected function xlcDeleteSystemLogs()
	{
		try
		{
			\IPS\Session::i()->csrfCheck();

			$form = new \IPS\Helpers\Form;

			$form->add( new \IPS\Helpers\Form\YesNo( 'xlc_delete_all_toggle', FALSE, FALSE, array(
				'togglesOff' => array( 'xlc_delete_or_categories', 'xlc_categories' ),
			) ) );

			$form->addDummy( '', \IPS\Member::loggedIn()->language()->addToStack( 'xlc_delete_or_categories' ), NULL, NULL, 'xlc_delete_or_categories' );

			$form->add( new \IPS\Helpers\Form\Select( 'xlc_categories', NULL, FALSE, array(
				'options'  => iterator_to_array( \IPS\Db::i()->select( 'DISTINCT(category) AS cat', 'core_log' )->setKeyField( 'cat' )->setValueField( 'cat' ) ),
				'multiple' => TRUE,
				'parse'    => 'normal',
			), NULL, NULL, NULL, 'xlc_categories' ) );

			$form->add( new \IPS\Helpers\Form\Checkbox( 'xlc_confirm_delete', FALSE, TRUE, array(), function( $val ) {
				if ( empty( $val ) )
				{
					throw new \DomainException( 'xlc_must_confirm_delete' );
				}
			} ) );

			if ( $values = $form->values() )
			{
				if ( $values['xlc_delete_all_toggle'] )
				{
					\IPS\Db::i()->delete( 'core_log' );
					\IPS\Session::i()->log( 'xlc_acplog__all_system_logs' );
				}
				elseif ( !empty( $values['xlc_categories'] ) )
				{
					\IPS\Db::i()->delete( 'core_log', \IPS\Db::i()->in( 'category', $values['xlc_categories'] ) );
					\IPS\Session::i()->log( 'xlc_acplog__system_categories', array( implode( ', ', $values['xlc_categories'] ) => FALSE ) );
				}

				\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=core&module=support&controller=systemLogs' ), 'deleted' );
			}

			\IPS\Output::i()->title  = \IPS\Member::loggedIn()->language()->addToStack( 'xlc_delete_system_logs' );
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

	/**
	 * Delete file-based logs — confirmation form
	 *
	 * @return	void
	 */
	protected function xlcDeleteFileLogs()
	{
		try
		{
			\IPS\Session::i()->csrfCheck();

			if ( \IPS\NO_WRITES )
			{
				\IPS\Output::i()->error( 'no_writes', '2XLC/1', 403, '' );
			}

			$form = new \IPS\Helpers\Form;

			\IPS\Member::loggedIn()->language()->words['xlc_confirm_delete_desc'] = \IPS\Member::loggedIn()->language()->addToStack( 'xlc_confirm_delete_files' );

			$form->add( new \IPS\Helpers\Form\Checkbox( 'xlc_confirm_delete', FALSE, TRUE, array(), function( $val ) {
				if ( empty( $val ) )
				{
					throw new \DomainException( 'xlc_must_confirm_delete' );
				}
			} ) );

			if ( $values = $form->values() )
			{
				if ( $values['xlc_confirm_delete'] )
				{
					$dir = \IPS\Log::fallbackDir();
					if ( is_dir( $dir ) )
					{
						foreach ( new \DirectoryIterator( $dir ) as $file )
						{
							if ( mb_substr( $file, 0, 1 ) !== '.' and $file != 'index.html' )
							{
								if ( !@unlink( $file->getPathname() ) )
								{
									\IPS\Output::i()->error(
										\IPS\Member::loggedIn()->language()->addToStack( 'xlc_file_could_not_delete', FALSE, array( 'sprintf' => $file->getPathname() ) ),
										'2XLC/2', 403, ''
									);
								}
							}
						}
					}

					\IPS\Session::i()->log( 'xlc_acplog__all_file_logs' );
				}

				\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=core&module=support&controller=systemLogs' ), 'deleted' );
			}

			\IPS\Output::i()->title  = \IPS\Member::loggedIn()->language()->addToStack( 'xlc_delete_file_logs' );
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
