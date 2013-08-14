-- Delete Spammers.
delete from GDN_User where ((CountDiscussions = 0 and CountComments = 0) or About != '' or Name = '[Deleted User]') and Admin = 0;
delete from GDN_User where CountVisits = 0 and Admin = 0;
delete from GDN_UserRole where UserID not in (select UserID from GDN_User);

-- Cleanup Data.
delete from GDN_Discussion where InsertUserID not in (select UserID from GDN_User);
delete from GDN_Comment where InsertUserID not in (select UserID from GDN_User);
delete from GDN_UserDiscussion where UserID not in (select UserID from GDN_User);
delete from GDN_Session where UserID not in (select UserID from GDN_User);
delete from GDN_Activity where ActivityUserID not in (select UserID from GDN_User);
delete from GDN_AntiSpamLog where UserID not in (select UserID from GDN_User);
delete from GDN_Log where InsertUserID not in (select UserID from GDN_User);

-- Need to update GDN_Category CountDiscussions and CountComments values with SQL too.