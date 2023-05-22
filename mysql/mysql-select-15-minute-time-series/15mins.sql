with recursive 15min_windows as (
    select 
        curdate() + interval 8 hour as start, 
        curdate() + interval 8 hour + interval 15 minute as end
    union
    select 
        end,
        end + interval 15 minute
    from 15min_windows
   where end < curdate() + interval 16 hour
)
select * from 15min_windows;