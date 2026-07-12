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
	public static function hookData()
	{
		return array();
	}

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
				\IPS\Output::i()->sidebar['actions']['xlcDeleteSystemLogs']['link'] = \IPS\Http\Url::internal( 'app=core&module=support&controller=systemLogs&do=xlcDeleteSystemLogs' )->csrf();
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
			$hasLogs = $this->xlcHasFallbackLogFiles();

			\IPS\Output::i()->sidebar['actions']['xlcDeleteFileLogs'] = array(
				'title' => 'xlc_delete_file_logs',
				'icon'  => 'trash',
			);

			if ( $hasLogs )
			{
				\IPS\Output::i()->sidebar['actions']['xlcDeleteFileLogs']['link'] = \IPS\Http\Url::internal( 'app=core&module=support&controller=systemLogs&do=xlcDeleteFileLogs' )->csrf();
				\IPS\Output::i()->sidebar['actions']['xlcDeleteFileLogs']['data'] = array( 'ipsDialog' => '', 'ipsDialog-title' => \IPS\Member::loggedIn()->language()->addToStack( 'xlc_delete_file_logs' ) );
			}
			else
			{
				\IPS\Output::i()->sidebar['actions']['xlcDeleteFileLogs']['class'] = 'ipsButton_disabled';
			}

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
	 * Check whether the fallback log directory contains files that can be deleted
	 *
	 * @return	bool
	 */
	protected function xlcHasFallbackLogFiles()
	{
		$dir = \IPS\Log::fallbackDir();
		if ( !is_dir( $dir ) )
		{
			return FALSE;
		}

		foreach ( new \DirectoryIterator( $dir ) as $file )
		{
			if ( $file->isFile() and mb_substr( $file->getFilename(), 0, 1 ) !== '.' and $file->getFilename() != 'index.html' )
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * Delete system logs with a "delete all" toggle and category multi-select
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
				$deleted = FALSE;

				if ( $values['xlc_delete_all_toggle'] )
				{
					\IPS\Db::i()->delete( 'core_log' );
					\IPS\Session::i()->log( 'xlc_acplog__all_system_logs' );
					$deleted = TRUE;
				}
				elseif ( !empty( $values['xlc_categories'] ) )
				{
					\IPS\Db::i()->delete( 'core_log', \IPS\Db::i()->in( 'category', $values['xlc_categories'] ) );
					\IPS\Session::i()->log( 'xlc_acplog__system_categories', array( implode( ', ', $values['xlc_categories'] ) => FALSE ) );
					$deleted = TRUE;
				}

				$redirectUrl = \IPS\Http\Url::internal( 'app=core&module=support&controller=systemLogs' );
				if ( $deleted )
				{
					\IPS\Output::i()->redirect( $redirectUrl, 'deleted' );
				}

				\IPS\Output::i()->redirect( $redirectUrl );
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
	 * Delete file-based logs with a confirmation form
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
				$deleted = FALSE;

				if ( $values['xlc_confirm_delete'] )
				{
					$dir = \IPS\Log::fallbackDir();
					if ( is_dir( $dir ) )
					{
						foreach ( new \DirectoryIterator( $dir ) as $file )
						{
							if ( !$file->isFile() or mb_substr( $file->getFilename(), 0, 1 ) === '.' or $file->getFilename() == 'index.html' )
							{
								continue;
							}

							if ( !@unlink( $file->getPathname() ) )
							{
								\IPS\Output::i()->error(
									\IPS\Member::loggedIn()->language()->addToStack( 'xlc_file_could_not_delete', FALSE, array( 'sprintf' => $file->getPathname() ) ),
									'2XLC/2', 403, ''
								);
							}

							$deleted = TRUE;
						}
					}

					if ( $deleted )
					{
						\IPS\Session::i()->log( 'xlc_acplog__all_file_logs' );
					}
				}

				$redirectUrl = \IPS\Http\Url::internal( 'app=core&module=support&controller=systemLogs' );
				if ( $deleted )
				{
					\IPS\Output::i()->redirect( $redirectUrl, 'deleted' );
				}

				\IPS\Output::i()->redirect( $redirectUrl );
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
