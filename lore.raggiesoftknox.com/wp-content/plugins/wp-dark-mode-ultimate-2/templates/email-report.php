<div class="email-wrap">

	<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#fff">
		<tbody>

		<!-- Header -->
		<tr>
			<td height="25" style="background:#222;padding:15px 5%;font-size:1px;border-collapse:collapse;margin:0">
				<p style="margin-top:0;margin-bottom:0">
					<span style="color:#fff;font-size:20px;font-family:inherit;font-weight:bold;text-transform:uppercase;text-decoration:initial;line-height:40px;letter-spacing:normal"><?php echo esc_html( $args['frequency'] ); ?> Dark Mode Usage Report:</span>
				</p>
			</td>
		</tr>

		<!--Header bottom border-->
		<tr>
			<td height="25"
				style="background:#29abe1;padding:0;font-size:1px;border-collapse:collapse;margin:0;height:5px"></td>
		</tr>

		<tr>
			<td valign="top"
				style="color:#595959;font-size:15px;font-family:HelveticaNeue,Roboto,sans-serif;font-weight:initial;text-transform:initial;text-decoration:initial;line-height:22px;letter-spacing:normal;padding:5%;margin-bottom:1rem;background:#fff">
				<div>
					<p dir="ltr"
					   style="margin-top:0;margin-bottom:0;margin-left:0;margin-right:0"><?php esc_html_e( 'Here is the dark mode usage report of last', 'wp-dark-mode-ultimate' ); ?> <?php echo esc_html( $args['length'] ); ?>
						days.</p>
					<p dir="ltr"
					   style="margin-top:0;margin-bottom:0;margin-left:0;margin-right:0"><?php esc_html_e( 'How much percentage of users use dark mode each day.', 'wp-dark-mode-ultimate' ); ?></p>

					<h3 style="color:#666;font-size:24px;font-family:inherit;font-weight:bold;text-transform:none;text-decoration:initial;line-height:31px;letter-spacing:normal"><?php esc_html_e( 'Dark Mode Usages:', 'wp-dark-mode-ultimate' ); ?></h3>

					<table cellpadding="0" cellspacing="0" style="width:100%;font-family:HelveticaNeue,Roboto,sans-serif;font-size:15px">

						<thead>
							<tr>
								<th style="text-align:right;width:120px;padding:8px;background:#eee;border:1px solid #eee;border-width:1px 0"><?php esc_html_e( 'Date', 'wp-dark-mode-ultimate' ); ?></th>
								<th style="text-align:center;width:120px;padding:8px;background:#eee;border:1px solid #eee;border-width:1px 0"><?php esc_html_e( 'Dark Mode Visitors', 'wp-dark-mode-ultimate' ); ?></th>
								<th style="text-align:center;width:120px;padding:8px;background:#eee;border:1px solid #eee;border-width:1px 0"><?php esc_html_e( 'Total Visitors', 'wp-dark-mode-ultimate' ); ?></th>
							</tr>
						</thead>

						<tbody>
						<?php

						if ( isset( $args['visits'] ) && is_array( $args['visits'] ) && ! empty( $args['visits'] ) ) {

							foreach ( $args['visits'] as $visit ) {
								// Format: 2025-02-18 | 100% (100 users) | 50% (50 users)
								printf(
									'<tr>
                                <td style="padding:8px;background:#fff;border:1px solid #eee; text-align:right;font-weight:bold">%s</td>
                                <td style="padding:8px;background:#fff;border:1px solid #eee; text-align:center">%s visitors (%s%%)</td>
								<td style="padding:8px;background:#fff;border:1px solid #eee; text-align:center">%s visitors</td>
                            </tr>',
									$visit['visit_date'],
									$visit['dark_mode_visits'],
									$visit['dark_mode_percentage'],
									$visit['total_visits']
								);

							}
						} else {
							echo '<tr><td colspan="2" style="padding:8px;background:#fff;border:1px solid #eee">' . esc_html__( 'No data found.', 'wp-dark-mode-ultimate' ) . '</td></tr>';
						}
						?>

						</tbody>
					</table>


				</div>
			</td>
		</tr>


		</tbody>
	</table>

</div>