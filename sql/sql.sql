

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
DROP TABLE rootids;
CREATE TEMP TABLE rootids
as
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
order by r.id asc;

SELECT r.rootid, m.id as metaid, m.property, m.type, m.value, m.value_n, m.value_t 
FROM 
rootids as r
LEFT JOIN metadata as m on r.rootid = m.id
GROUP BY r.rootid, m.id, m.property, m.type, m.value, m.value_n, m.value_t 
order by r.rootid




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

SET repo.identifier = 'https://id.acdh.oeaw.ac.at/uuid/6464173b-0e52-720c-9491-8f889dac8cca';

CREATE OR REPLACE FUNCTION public.detail_view_func(_identifier text)
    RETURNS table (id bigint, property text, type text, value text, relvalue text, acdhid  text)
    
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
	select dm.id, dm.property, dm.type, dm.value, dmr.value as relvalue, dmr.acdhid 
	from detail_meta as dm
	left join detail_meta_rel as dmr on dmr.id = dm.value
	order by property; 
END
$func$
LANGUAGE 'plpgsql';

ALTER FUNCTION public.detail_view_func()
    OWNER TO norbert;
