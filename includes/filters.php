<?php
/**
 * Functions that filter the Contextual Related Posts clauses
 *
 * @package CRP_Taxonomy
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Filter JOIN clause of CRP query to add taxonomy tables.
 *
 * @since 1.0.0
 *
 * @param string $join    JOIN clause.
 * @param int    $post_id Post ID.
 * @param array  $args    Arguments array.
 * @return string  Filtered JOIN clause
 */
function crpt_crp_posts_join( $join, $post_id, $args ) {
	global $wpdb;

	// Return if we have no tag / category or taxonomy to be matched.
	if ( empty( $args['crpt_same_taxes'] ) && empty( $args['crpt_tag'] ) && empty( $args['crpt_category'] ) && empty( $args['crpt_taxes'] ) ) {
		return $join;
	}

	$sql  = $join;
	$sql .= " INNER JOIN $wpdb->term_relationships AS crpt_tr ON ($wpdb->posts.ID = crpt_tr.object_id) ";
	$sql .= " INNER JOIN $wpdb->term_taxonomy AS crpt_tt ON (crpt_tr.term_taxonomy_id = crpt_tt.term_taxonomy_id) ";

	if ( $args['crpt_match_all'] ) {
		$sql .= " INNER JOIN $wpdb->terms AS crpt_t ON (crpt_tt.term_id = crpt_t.term_id) ";
	}

	return $sql;
}
add_filter( 'crp_posts_join', 'crpt_crp_posts_join', 10, 3 );


/**
 * Filter WHERE clause of CRP query to limit posts only to category of current post.
 *
 * @since 1.0.0
 *
 * @param string $where   WHERE clause.
 * @param int    $post_id Post ID.
 * @param array  $args    Arguments array.
 * @return  string  Filtered WHERE clause
 */
function crpt_crp_posts_where( $where, $post_id, $args ) {
	global $wpdb, $post;

	$term_ids          = array();
	$taxonomies        = array();
	$current_post_cats = array();

	// Return false if current category is in exclude_on_categories.
	if ( ! empty( $args['exclude_on_categories'] ) ) {

		$current_post_category = get_the_category();

		foreach ( $current_post_category as $cat ) {
			$current_post_cats[] = $cat->cat_ID;
		}

		$exclude_categories = explode( ',', $args['exclude_on_categories'] );

		if ( ! empty( array_intersect( $current_post_cats, $exclude_categories ) ) ) {
			return ' AND false ';
		}
	}

	// Return if we have no tag / category or taxonomy to be matched.
	if ( empty( $args['crpt_same_taxes'] ) && empty( $args['crpt_tag'] ) && empty( $args['crpt_category'] ) && empty( $args['crpt_taxes'] ) ) {
		return $where;
	}

	if ( isset( $args['crpt_category'] ) && $args['crpt_category'] ) {
		$taxonomies[] = 'category';
	}

	if ( isset( $args['crpt_tag'] ) && $args['crpt_tag'] ) {
		$taxonomies[] = 'post_tag';
	}

	if ( isset( $args['crpt_taxes'] ) && $args['crpt_taxes'] ) {
		$crpt_taxes = explode( ',', $args['crpt_taxes'] );
		$taxonomies = array_merge( $taxonomies, $crpt_taxes );
	}

	if ( isset( $args['crpt_same_taxes'] ) && $args['crpt_same_taxes'] ) {
		$crpt_same_taxes = explode( ',', $args['crpt_same_taxes'] );
		$taxonomies      = array_merge( $taxonomies, $crpt_same_taxes );
	}

	// Get the taxonomies used by the post type.
	$current_taxonomies = get_object_taxonomies( $post );

	// Temp variable used in crpt_crp_posts_having(). We only want to limit this for taxonomies linked to the current post type.
	$args['crpt_taxonomy_count'] = count( array_intersect( $current_taxonomies, $taxonomies ) );

	// Get the terms for the current post.
	$terms = wp_get_object_terms( $post->ID, $taxonomies );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		$args['crpt_taxonomy_count'] = 0;
		return $where;
	} else {
		$sql = '';

		if ( $args['crpt_match_all'] ) {
			// Limit to posts matching all current taxonomy terms.
			$term_strings   = array();
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
				$args['crpt_taxonomy_count'] = 0;
			}
		} else {
			// Limit to posts matching any current taxonomy term.
			$term_ids = array_unique( wp_list_pluck( $terms, 'term_id' ) );

			if ( count( $term_ids ) ) {
				$tax_ids = implode( ',', $term_ids );
				$sql    .= " AND crpt_tt.term_id IN ($tax_ids)";
			}
		}

		return $where . ' ' . $sql;
	}
}
add_filter( 'crp_posts_where', 'crpt_crp_posts_where', 10, 3 );


/**
 * Filter GROUP BY clause of CRP query.
 *
 * @since   1.1.1
 *
 * @param string $groupby GROUP BY clause.
 * @param int    $post_id Post ID.
 * @param array  $args    Arguments array.
 * @return string Filtered GROUP BY clause
 */
function crpt_crp_posts_groupby( $groupby, $post_id, $args ) {
	global $wpdb;

	if ( isset( $args['crpt_match_all'] ) && $args['crpt_match_all'] && (
		! empty( $args['crpt_same_taxes'] ) ||
		! empty( $args['crpt_tag'] ) ||
		! empty( $args['crpt_category'] ) ||
		! empty( $args['crpt_taxes'] )
		) ) {
		$groupby .= " $wpdb->posts.ID";
	}

	return $groupby;
}
add_filter( 'crp_posts_groupby', 'crpt_crp_posts_groupby', 10, 3 );


/**
 * Filter ORDER BY clause of CRP query.
 *
 * @since   1.3.0
 *
 * @param string $orderby  ORDER BY clause.
 * @param int    $post_id  Post ID.
 * @param array  $args     Arguments array.
 * @return string Filtered ORDER BY clause
 */
function crpt_crp_posts_orderby( $orderby, $post_id, $args ) {

	if ( isset( $args['crpt_match_all'] ) && $args['crpt_match_all'] && isset( $args['crpt_taxonomy_count'] ) && $args['crpt_taxonomy_count'] ) {

		// Add a comma if $orderby is not empty.
		if ( ! empty( $orderby ) ) {
			$orderby .= ',';
		}
		$orderby .= $args['crp_posts_match'] . ' DESC ';
	}

	return $orderby;
}
add_filter( 'crp_posts_orderby', 'crpt_crp_posts_orderby', 10, 3 );


/**
 * Filter HAVING clause of CRP query.
 *
 * @since   1.2.0
 *
 * @param string $having  HAVING clause.
 * @param int    $post_id Post ID.
 * @param array  $args    Arguments array.
 * @return string Filtered HAVING clause
 */
function crpt_crp_posts_having( $having, $post_id, $args ) {
	global $wpdb;

	$crpt_no_of_taxes = isset( $args['crpt_no_of_taxes'] ) ? $args['crpt_no_of_taxes'] : 1;

	if ( isset( $args['crpt_match_all'] ) && $args['crpt_match_all'] && isset( $args['crpt_taxonomy_count'] ) && $args['crpt_taxonomy_count'] ) {
		$having .= $wpdb->prepare( ' ( COUNT(DISTINCT crpt_tt.taxonomy) = %d AND COUNT(DISTINCT crpt_tt.term_id) >= %d ) ', $args['crpt_taxonomy_count'], $crpt_no_of_taxes );
	}

	return $having;
}
add_filter( 'crp_posts_having', 'crpt_crp_posts_having', 10, 3 );


/**
 * Disable FULLTEXT matching.
 *
 * @since 1.1.0
 *
 * @param array      $match MATCH clause.
 * @param string     $stuff Content being matched.
 * @param int|string $post_id Post ID.
 * @return array Filtered array of CRP Settings
 */
function crpt_crp_posts_match( $match, $stuff, $post_id ) {

	// If matching all taxonomies, we store $match temporarily so it can be globally accessed.
	if ( false !== strpos( $match, 'AND' ) ) {
		$match_no_and = substr_replace( $match, '', strpos( $match, 'AND' ), 3 );
	}
	$args['crp_posts_match'] = $match_no_and;

	$post_type = get_post_type( $post_id );

	if ( isset( $args['crpt_disable_contextual'] ) && $args['crpt_disable_contextual'] ) {

		/* If post or page and we're not disabling custom post types */
		if ( ( 'post' === $post_type || 'page' === $post_type ) && ( $args['crpt_disable_contextual_cpt'] ) ) {
			return $match;
		}

		return ' ';

	}

	return $match;
}
add_filter( 'crp_posts_match', 'crpt_crp_posts_match', 10, 3 );


/**
 * Disable the output on selected categories.
 *
 * @since 1.6.0
 *
 * @param bool   $short_circuit Short circuit filter.
 * @param object $post          Current Post object.
 * @param array  $args          Arguments array.
 * @return bool Updated short circuit flag.
 */
function crpt_short_circuit( $short_circuit, $post, $args ) {

	$current_post_cats = array();

	// Return false if current category is in exclude_on_categories.
	if ( ! empty( $args['exclude_on_categories'] ) ) {

		$current_post_category = get_the_category();

		foreach ( $current_post_category as $cat ) {
			$current_post_cats[] = $cat->cat_ID;
		}

		$exclude_categories = explode( ',', $args['exclude_on_categories'] );

		if ( ! empty( array_intersect( $current_post_cats, $exclude_categories ) ) ) {
			return true;
		}
	}

	return $short_circuit;
}
add_filter( 'get_crp_short_circuit', 'crpt_short_circuit', 10, 3 );
