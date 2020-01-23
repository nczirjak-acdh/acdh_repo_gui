


//////////// the call of the root view ///////////////////////////
SET repo.language = 'de';
select * from rootids_view;


//////////////////////////////////////// ROOT VIEW /////////////////////////////////
/*
SET repo.language = 'de';
select * from rootids_view;

CREATE VIEW rootids_view AS
m.lang = cast(current_setting('repo.language') as varchar) and
*/

SET repo.language = '';

select DISTINCT(r.id)
, (Select m.value from metadata as m where  id = r.id and m.property = 'https://vocabs.acdh.oeaw.ac.at/schema#hasTitle' LIMIT 1) as title
, (Select m.value from metadata as m where id = r.id and m.property = 'https://vocabs.acdh.oeaw.ac.at/schema#hasDescription' LIMIT 1) as description
, (Select m.value from metadata as m where id = r.id and m.property = 'https://vocabs.acdh.oeaw.ac.at/schema#hasTitleImage' LIMIT 1) as titleImage
, (Select string_agg(m.value, ',') from metadata as m where id = r.id and m.property = 'https://vocabs.acdh.oeaw.ac.at/schema#hasContributor' LIMIT 1) as contributor
, (Select string_agg(m.value, ',') from metadata as m where id = r.id and m.property = 'https://vocabs.acdh.oeaw.ac.at/schema#hasAuthor' LIMIT 1) as author
, (Select m.value from metadata as m where id = r.id and m.property = 'https://vocabs.acdh.oeaw.ac.at/schema#hasAccessRestriction' LIMIT 1) as accessRestriction
, (Select ids from identifiers as i where i.id = m.id and i.ids LIKE '%//id.acdh.oeaw.ac.at/uuid/%' LIMIT 1) as identifier
, (Select ids from identifiers as i where i.id = m.id and i.ids LIKE '%//repo.%.oeaw.ac.at/%' LIMIT 1) as repourl
, (Select string_agg(ids, ',')  from identifiers as i where i.id = m.id) as identifiers
, (Select m.value from metadata as m where id = r.id and m.property = 'https://vocabs.acdh.oeaw.ac.at/schema#hasAvailableDate' LIMIT 1) as availableDate
, (Select m.value from metadata as m where id = r.id and m.property = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type' and m.value LIKE '%//vocabs.acdh.oeaw.ac.at/%' LIMIT 1) as acdhtype
from metadata as m
left join relations as r on r.id = m.id
where
 m.property = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type' 
and m.value = 'https://vocabs.acdh.oeaw.ac.at/schema#Collection'
and r.property != 'https://vocabs.acdh.oeaw.ac.at/schema#isPartOf'
and r.id NOT IN ( 
	SELECT DISTINCT(r.id) from metadata as m left join relations as r on r.id = m.id
	where
 		m.property = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type' 
		and m.value = 'https://vocabs.acdh.oeaw.ac.at/schema#Collection'
		and r.property = 'https://vocabs.acdh.oeaw.ac.at/schema#isPartOf'
)
order by r.id asc;


////////////////// root view all metadata ///////////////////////////////

/*
* root view all metadata 
*/
CREATE OR REPLACE FUNCTION public.root_view_func()
    RETURNS table (id bigint, property text, type text, value text, acdhid  text)
AS $func$
BEGIN
/* get root ids */
DROP TABLE IF EXISTS  rootids;
CREATE TEMP TABLE rootids AS (
	select DISTINCT(r.id) as rootid
	from metadata as m
	left join relations as r on r.id = m.id
	where
 	m.property = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type' 
	and m.value = 'https://vocabs.acdh.oeaw.ac.at/schema#Collection'
	and r.property != 'https://vocabs.acdh.oeaw.ac.at/schema#isPartOf'
	and r.id NOT IN ( 
		SELECT DISTINCT(r.id) from metadata as m left join relations as r on r.id = m.id
		where
 			m.property = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type' 
			and m.value = 'https://vocabs.acdh.oeaw.ac.at/schema#Collection'
			and r.property = 'https://vocabs.acdh.oeaw.ac.at/schema#isPartOf'
	)
	order by r.id asc
);

/* get root raw metadata by the rootids */
DROP TABLE IF EXISTS root_meta;
CREATE TEMPORARY TABLE root_meta AS (
select mv.id, mv.property, mv.type, mv.value
	from identifiers as i
	inner join metadata_view as mv on mv.id = i.id
	inner join rootids as ri on ri.rootid = i.id
	union
	select m.id, m.property, m.type, m.value
	from identifiers as i
	inner join metadata as m on m.id = i.id
	inner join rootids as ri on ri.rootid = i.id
	
);

/* get the root relation properties */
DROP TABLE IF EXISTS root_meta_rel;
	CREATE TEMPORARY TABLE root_meta_rel AS (
	select DISTINCT(m.id), m.value, i.ids as acdhId, rm.id as resId, rm.property, rm.type
	from metadata as m
	left join root_meta as rm on CAST(rm.value as INT) = m.id and m.property = 'https://vocabs.acdh.oeaw.ac.at/schema#hasTitle'
	left join identifiers as i on i.id = m.id and i.ids LIKE CAST('%/id.acdh.oeaw.ac.at/uuid/%' as varchar)
	where rm.type = 'REL'  );

RETURN QUERY
Select
	rm.id, rm.property, rm.type, rm.value,  NULL::text AS acdhId
from root_meta as rm
where rm.type != 'REL'
UNION
SELECT
	rmr.resid as id, rmr.property, rmr.type, rmr.value, rmr.acdhId
from root_meta_rel as rmr;

END
$func$
LANGUAGE 'plpgsql';

ALTER FUNCTION public.root_view_func()
OWNER TO norbert;




//////////////////////////////////////// detail VIEW /////////////////////////////////
SET repo.identifier = 'https://id.acdh.oeaw.ac.at/uuid/6ad20f66-1160-c599-bcea-6b2073095576';


CREATE VIEW detail_view AS
Select 
m.id, m.mid, m.property, m.type, m.value_n, m.value_t, m.value
from 
identifiers as i
Left join metadata as m on m.id = i.id
where 
i.ids LIKE cast(current_setting('repo.identifier') as varchar)
order by m.property;




CREATE TEMPORARY TABLE IF NOT EXISTS detail_data AS (select id, property, type, value
from detail_view)


################################DETAIL VIEW METADATA FUNCTION ########################################################

/*
* DETAIL VIEW METADATA FUNCTION 
*/
CREATE OR REPLACE FUNCTION public.detail_view_func(_identifier text)
    RETURNS table (id bigint, property text, type text, value text, relvalue text, acdhid text, accessRestriction text )
    
AS $func$
BEGIN
	DROP TABLE IF EXISTS detail_meta;
	CREATE TEMPORARY TABLE detail_meta AS (
	select mv.id, mv.property, mv.type, mv.value
	from identifiers as i
	inner join metadata_view as mv on mv.id = i.id
	where i.ids = _identifier
	union
	select m.id, m.property, m.type, m.value
	from identifiers as i
	inner join metadata as m on m.id = i.id
	where i.ids = _identifier
	);

	DROP TABLE IF EXISTS detail_meta_rel;
	CREATE TEMPORARY TABLE detail_meta_rel AS (
	select DISTINCT(CAST(m.id as VARCHAR)), m.value,  i.ids as acdhId
	from metadata as m
	left join detail_meta as dm on CAST(dm.value as INT) = m.id and m.property = 'https://vocabs.acdh.oeaw.ac.at/schema#hasTitle'
	left join identifiers as i on i.id = m.id and i.ids LIKE CAST('%/id.acdh.oeaw.ac.at/uuid/%' as varchar)
	where dm.type = 'REL' );
	
	RETURN QUERY
	select dm.id, dm.property, dm.type, dm.value, dmr.value as relvalue, dmr.acdhid,
	(select r.val from raw as r where r.prop = 'https://vocabs.acdh.oeaw.ac.at/schema#hasAccessRestriction' and r.id = dm.id ) as accessRestriction
	from detail_meta as dm
	left join detail_meta_rel as dmr on dmr.id = dm.value	
	order by property; 
END
$func$
LANGUAGE 'plpgsql';

ALTER FUNCTION public.detail_view_func()
    OWNER TO norbert;


################################ CHILD VIEW METADATA FUNCTION ########################################################

f.e: select * from child_view_func('https://id.acdh.oeaw.ac.at/uuid/57777494-57e5-6f8f-c170-461cecbb44b3', '10', '0', 'value asc', 'https://vocabs.acdh.oeaw.ac.at/schema#hasTitle');


CREATE OR REPLACE FUNCTION public.child_view_func(_parentid text, _limit int, _page int, _orderby text, _orderprop text )
    RETURNS table (id bigint, property text, type text, value text, acdhid  text)
AS $func$
BEGIN
	/* get child ids */
	DROP TABLE IF EXISTS child_ids;
	CREATE TEMPORARY TABLE child_ids(orderid serial, childid int);
	INSERT INTO child_ids( 
		select 
			row_number() over (order by mv.value asc) as orderid, 
			r.id as childid
		from relations as r
		left join identifiers as i on i.id = r.target_id 
		left join metadata_view as mv on mv.id = r.id
		where r.property = 'https://vocabs.acdh.oeaw.ac.at/schema#isPartOf'
		and mv.property = _orderprop
		and i.ids = _parentid
		order by _orderby
		limit _limit
		offset _page
	);
	
	/* get child raw metadata by the childids */
	DROP TABLE IF EXISTS child_meta;
	CREATE TEMPORARY TABLE child_meta AS (
		select 
			mv.id, mv.property, mv.type, mv.value
		from child_ids as ci 
		left join identifiers as i on ci.childid = i.id
		inner join metadata_view as mv on mv.id = i.id
		union
		select m.id, m.property, m.type, m.value
		from child_ids as ci 
		left join identifiers as i on ci.childid = i.id
		inner join metadata as m on m.id = i.id
	);
	
	/* get the child relation properties */
	DROP TABLE IF EXISTS child_meta_rel;
	CREATE TEMPORARY TABLE child_meta_rel AS (
		select DISTINCT(m.id), m.value, i.ids as acdhId, cm.id as resId, cm.property, cm.type
		from metadata as m
		left join child_meta as cm on CAST(cm.value as INT) = m.id and m.property = 'https://vocabs.acdh.oeaw.ac.at/schema#hasTitle'
		left join identifiers as i on i.id = m.id and i.ids LIKE CAST('%/id.acdh.oeaw.ac.at/uuid/%' as varchar)
		where cm.type = 'REL'  
	);

	DROP TABLE IF EXISTS child_final;
	CREATE TEMPORARY TABLE child_final AS (
		Select
			cm.id as id, cm.property, cm.type, cm.value,  NULL::text AS acdhId
		from 
			child_ids as ci
		left join 
			child_meta as cm on cm.id = ci.childid
		where 
			cm.type != 'REL'
		UNION
		SELECT
			cmr.resid as id, cmr.property, cmr.type, cmr.value, cmr.acdhId
		from child_ids as ci
		left join child_meta_rel as cmr on cmr.id = ci.childid 
	);
	
	RETURN QUERY
		select chf.*
		from child_ids as ci
		left join child_final as chf on chf.id = ci.childid
		order by ci.orderid asc
	;
END
$func$
LANGUAGE 'plpgsql';





//////////// CHILD VIEW SUM for paging //////////////////////////////


CREATE OR REPLACE FUNCTION public.child_view_sum_func(_parentid text)
    RETURNS table (num bigint)
AS $func$
BEGIN
/* get child ids */
RETURN QUERY
	select 
		COUNT(r.id) as num
	from relations as r
	left join identifiers as i on i.id = r.target_id 
	where r.property = 'https://vocabs.acdh.oeaw.ac.at/schema#isPartOf'
	and i.ids = _parentid;
END
$func$
LANGUAGE 'plpgsql';

