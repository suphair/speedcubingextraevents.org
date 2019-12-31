update Command Com  
inner join(
select distinct
Command_t.ID,
case 
when Command_t.vCompetitors=2
then
	Concat(trim(SUBSTRING_INDEX(C1.Name, '(', 1)),', ',trim(SUBSTRING_INDEX(C2.Name, '(', 1)))
else
	Concat(trim(SUBSTRING_INDEX(C1.Name, '(', 1)),', ',trim(SUBSTRING_INDEX(C2.Name, '(', 1)),', ',trim(SUBSTRING_INDEX(C3.Name, '(', 1)))
end vName,
case 
when Command_t.vCompetitors=2
then
	Concat(C1.ID,', ',C2.ID)
else
	Concat(C1.ID,', ',C2.ID,', ',C3.ID)
end vCompetitorIDs
 from 
( select Com.ID,Com. vCompetitors from Command Com join CommandCompetitor CC on CC.Command=Com.ID group by Com.ID ) Command_t
join CommandCompetitor CC1 on CC1.Command=Command_t.ID
join Competitor C1 on CC1.Competitor=C1.ID
join CommandCompetitor CC2 on CC2.Command=Command_t.ID
join Competitor C2 on CC2.Competitor=C2.ID and C2.Name>C1.Name
left outer join CommandCompetitor CC3 on CC3.Command=Command_t.ID
left outer join Competitor C3 on CC3.Competitor=C3.ID and C3.Name>C2.Name

where C3.ID is not null or Command_t.vCompetitors=2
)c on c.ID=Com.ID and (BINARY Com.vCompetitorIDs!= BINARY c.vCompetitorIDs or BINARY Com.vName!= BINARY c.vName)
Set Com.vName=c.vName,Com.vCompetitorIDs=c.vCompetitorIDs
