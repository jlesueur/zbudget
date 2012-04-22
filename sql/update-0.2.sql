begin;

alter table expense add column credit int2;
alter table expense alter column credit set default 0;
update expense set credit = 0 where amount >= 0;
update expense set credit = 1, amount = amount * -1 where amount < 0;

commit;