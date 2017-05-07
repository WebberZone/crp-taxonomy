<?php
/**
 * Functions that filter the Contextual Related Posts clauses
 *
 * @package CRP_Taxonomy
 */

/**
 * Filter JOIN clause of CRP query to add taxonomy tables.
 *
 * @since 1.0.0
 *
 * @param	string $join JOIN clause.
 * @return	string	Filtered JOIN clause
 */
function crpt_crp_posts_join( $join ) {
	global $wpdb, $crp_settings;

	// Return if we have no tag / category or taxonomy to be matched.
	if ( empty( $crp_settings['crpt_tag'] ) && empty( $crp_settings['crpt_category'] ) && empty( $crp_settings['crpt_taxes'] ) ) {
		return $join;
	}

	$sql = $join;
	$sql .= " INNER JOIN $wpdb->term_relationships AS crpt_tr ON ($wpdb->posts.ID = crpt_tr.object_id) ";
	$sql .= " INNER JOIN $wpdb->term_taxonomy AS crpt_tt ON (crpt_tr.term_taxonomy_id = crpt_tt.term_taxonomy_id) ";

	if ( $crp_settings['crpt_match_all'] ) {
		$sql .= " INNER JOIN $wpdb->terms AS crpt_t ON (crpt_tt.term_id = crpt_t.term_id) ";
	}

	return $sql;
}
add_filter( 'crp_posts_join', 'crpt_crp_posts_join' );


/**
 * Filter WHERE clause of CRP query to limit posts only to category of current post.
 *
 * @since 1.0.0
 *
 * @param	string $where WHERE clause.
 * @return	string	Filtered WHERE clause
 */
function crpt_crp_posts_where( $where ) {
	global $wpdb, $post, $crp_settings;

	$term_ids = array();
	$taxonomies = array();

	// Return if we have no tag / category or taxonomy to be matched.
	if ( empty( $crp_settings['crpt_tag'] ) && empty( $crp_settings['crpt_category'] ) && empty( $crp_settings['crpt_taxes'] ) ) {
		return $where;
	}

	if ( $crp_settings['crpt_category'] ) {
		$taxonomies[] = 'category';
	}

	if ( $crp_settings['crpt_tag'] ) {
		$taxonomies[] = 'post_tag';
	}

	if ( $crp_settings['crpt_taxes'] ) {
		$crpt_taxes = explode( ',', $crp_settings['crpt_taxes'] );
		$taxonomies = array_merge( $taxonomies, $crpt_taxes );
	}

	// Get the taxonomies used by the post type.
	$current_taxonomies = get_object_taxonomies( $post );

	// Temp variable used in crpt_crp_posts_having(). We only want to limit this for taxonomies linked to the current post type.
	$crp_settings['crpt_taxonomy_count'] = count( array_intersect( $current_taxonomies, $taxonomies ) );

	// Get the terms for the current post.
	$terms = wp_get_object_terms( $post->ID, $taxonomies );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		$crp_settings['crpt_taxonomy_count'] = 0;
		return $where;
	} else {
		$sql = '';

		if ( $crp_settings['crpt_match_all'] ) {
			// Limit to posts matching all current taxonomy terms.
			$term_strings = array();
			$selected_taxes = $taxonomies;

			if ( count( $selected_taxes ) ) {
				// Find the matching selected taxonomies
				// Then add the term_id for that tax to the array.
				foreach ( $terms as $term ) {
					if ( in_array( $term->taxonomy, $selected_taxes, true ) ) {
						$term_strings[] = $wpdb->prepare( '%s', $term->taxonomy . '/' . $term->term_id );
					}
				}
			}

			// Credit for solution: http://wordpress.stackexchange.com/questions/8503/optimize-multiple-taxonomy-term-mysql-query
			// This SQL will match all posts that have the specified term_id in a particular taxonomy by converting
			// the data into a string and matching against that.  Now this does return nearly the same results
			// as if crpt_match_all was false, however a HAVING statement added later reduces the dataset down
			// to only posts that match at least 1 term in each the taxonomy.
			$term_count = count( $term_strings );

			if ( $term_count ) {
				$sql .= " AND CONCAT(crpt_tt.taxonomy, '/', crpt_t.term_id) IN (";
				$sql .= implode( ',', $term_strings );
				$sql .= ')';
			} else {
				$crp_settings['crpt_taxonomy_count'] = 0;
			}
		} else {
			// Limit to posts matching any current taxonomy term.
			$term_ids = array_unique( wp_list_pluck( $terms, 'term_id' ) );

			if ( count( $term_ids ) ) {
				$tax_ids = implode( ',', $term_ids );
				$sql .= " AND crpt_tt.term_id IN ($tax_ids)";
			}
		}// End if().

		return $where . ' ' . $sql;
	}// End if().
}
add_filter( 'crp_posts_where', 'crpt_crp_posts_where' );


/**
 * Filter GROUP BY clause of CRP query.
 *
 * @since	1.1.1
 *
 * @param  string $groupby GROUP BY clause.
 * @return string Filtered GROUP BY clause
 */
function crpt_crp_posts_groupby( $groupby ) {
	global $wpdb, $crp_settings;

	if ( isset( $crp_settings['crpt_match_all'] ) && $crp_settings['crpt_match_all'] && ( $crp_settings['crpt_tag'] || $crp_settings['crpt_category'] || $crp_settings['crpt_taxes'] ) ) {
		$groupby .= " $wpdb->posts.ID";
	}

	return $groupby;
}
add_filter( 'crp_posts_groupby', 'crpt_crp_posts_groupby' );


/**
 * Filter ORDER BY clause of CRP query.
 *
 * @since	1.3.0
 *
 * @param  string $orderby ORDER BY clause.
 * @return string Filtered ORDER BY clause
 */
function crpt_crp_posts_orderby( $orderby ) {
	global $crp_settings;

	if ( isset( $crp_settings['crpt_match_all'] ) && $crp_settings['crpt_match_all'] && isset( $crp_settings['crpt_taxonomy_count'] ) && $crp_settings['crpt_taxonomy_count'] ) {

		// Add a comma if $orderby is not empty.
		if ( ! empty( $orderby ) ) {
			$orderby .= ',';
		}
		$orderby .= $crp_settings['crp_posts_match'] . ' DESC ';
	}

	return $orderby;
}
add_filter( 'crp_posts_orderby', 'crpt_crp_posts_orderby', 10, 1 );


/**
 * Filter HAVING clause of CRP query.
 *
 * @since	1.2.0
 *
 * @param  string $having HAVING clause.
 * @return string Filtered HAVING clause
 */
function crpt_crp_posts_having( $having ) {
	global $wpdb, $crp_settings;

	if ( isset( $crp_settings['crpt_match_all'] ) && $crp_settings['crpt_match_all'] && isset( $crp_settings['crpt_taxonomy_count'] ) && $crp_settings['crpt_taxonomy_count'] ) {
		$having .= $wpdb->prepare( ' COUNT(DISTINCT crpt_tt.taxonomy) = %d', $crp_settings['crpt_taxonomy_count'] );
	}

	return $having;
}
add_filter( 'crp_posts_having', 'crpt_crp_posts_having', 10, 1 );


/**
 * Disable FULLTEXT matching.
 *
 * @since 1.1.0
 *
 * @param array      $match MATCH clause.
 * @param string     $stuff Content being matched.
 * @param int|string $postid Post ID.
 * @return array Filtered array of CRP Settings
 */
function crpt_crp_posts_match( $match, $stuff, $postid ) {
	global $crp_settings;

	// If matching all taxonomies, we store $match temporarily so it can be globally accessed.
	if ( false !== strpos( $match, 'AND' ) ) {
	    $match_no_and = substr_replace( $match, '', strpos( $match, 'AND' ), 3 );
	}
	$crp_settings['crp_posts_match'] = $match_no_and;

	$post_type = get_post_type( $postid );

	if ( isset( $crp_settings['crpt_disable_contextual'] ) && $crp_settings['crpt_disable_contextual'] ) {

		/* If post or page and we're not disabling custom post types */
		if ( ( 'post' === $post_type || 'page' === $post_type ) && ( $crp_settings['crpt_disable_contextual_cpt'] ) ) {
			return $match;
		}

		return ' ';

	}

	return $match;
}
add_filter( 'crp_posts_match', 'crpt_crp_posts_match', 10, 3 );

