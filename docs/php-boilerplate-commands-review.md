# Trainit PHP Boilerplate Commands for Review

Status: **UNEXECUTED DOMAIN COMMANDS**

The master framework import, Trainit configuration, public-page route conversion, and design merge were completed on 14 August 2026. The PHP generator commands below have **not** been executed. They are a proposed scaffold sequence for review, not an approved migration.

## Approval gate

Do not execute these commands until the entity names, role IDs, approval states, pricing rules, money representation, nullable fields, indexes, access rules, and database backup/cutover procedure are approved.

The generator writes a model, controller, CRUD views, JavaScript, route file, Administrator navigation item, and dashboard card. Generated fields are NOT NULL, and a generated model creates its table when first instantiated. Therefore, each generated model must be reviewed and patched before the application or that model is booted.

Generator syntax verified in this imported framework:

~~~text
php boilerplate/generate_files.php <singular_table_name> '<field>:<type>[,<field>:<type> ...]'
~~~

Supported scalar types: text, integer, decimal, date, datetime, time, boolean.

Preferred foreign-key syntax: <field>:fk:<RelatedModel>.

## Phase 1 — catalogue and vetting

~~~text
# UNEXECUTED
php boilerplate/generate_files.php servicecategory 'code:text,name:text,description:text,sort_order:integer'
php boilerplate/generate_files.php serviceoffering 'servicecategory:fk:Servicecategory,code:text,name:text,description:text,default_worker_level:text'
php boilerplate/generate_files.php expertise 'code:text,name:text,description:text'
php boilerplate/generate_files.php serviceexpertise 'serviceoffering:fk:Serviceoffering,expertise:fk:Expertise,importance:text'
php boilerplate/generate_files.php serviceprice 'serviceoffering:fk:Serviceoffering,worker_level:text,currency:text,hourly_rate:decimal,effective_from:date,effective_to:date'
php boilerplate/generate_files.php servicelevelpolicy 'serviceoffering:fk:Serviceoffering,priority:text,response_minutes:integer,resolution_hours:decimal'
php boilerplate/generate_files.php accountapplication 'user:fk:User,requested_account_type:text,application_status:text,submitted_at:datetime,reviewed_by:fk:User,reviewed_at:datetime,review_notes:text'
php boilerplate/generate_files.php professionalapplication 'accountapplication:fk:Accountapplication,requested_level:text,biography:text,highest_qualification:text,years_experience:decimal,weekly_capacity_hours:decimal,availability_status:text'
php boilerplate/generate_files.php qualification 'professionalapplication:fk:Professionalapplication,title:text,institution:text,field_of_study:text,start_date:date,completion_date:date,document_key:text,verification_status:text'
php boilerplate/generate_files.php workexperience 'professionalapplication:fk:Professionalapplication,organization:text,role_title:text,start_date:date,end_date:date,description:text,referee_name:text,referee_contact:text'
php boilerplate/generate_files.php professionalexpertise 'professionalapplication:fk:Professionalapplication,expertise:fk:Expertise,proficiency_level:text,years_experience:decimal'
php boilerplate/generate_files.php professionalprofile 'user:fk:User,accountapplication:fk:Accountapplication,worker_level:text,vetting_status:text,availability_status:text,weekly_capacity_hours:decimal,approved_by:fk:User,approved_at:datetime'
php boilerplate/generate_files.php clientapplication 'accountapplication:fk:Accountapplication,legal_name:text,trading_name:text,registration_number:text,tax_number:text,industry:text,business_description:text,address:text,city:text,country:text,website:text,primary_phone:text'
php boilerplate/generate_files.php clientapplicationservice 'clientapplication:fk:Clientapplication,servicecategory:fk:Servicecategory,notes:text'
~~~

## Phase 2 — approved clients and subscriptions

~~~text
# UNEXECUTED
php boilerplate/generate_files.php clientorganization 'clientapplication:fk:Clientapplication,legal_name:text,trading_name:text,registration_number:text,tax_number:text,industry:text,billing_email:text,address:text,city:text,country:text,timezone:text'
php boilerplate/generate_files.php clientmembership 'clientorganization:fk:Clientorganization,user:fk:User,member_role:text,is_primary:boolean,effective_from:date,effective_to:date'
php boilerplate/generate_files.php clientserviceplan 'clientorganization:fk:Clientorganization,serviceoffering:fk:Serviceoffering,plan_name:text,currency:text,monthly_fee:decimal,included_hours:decimal,associate_rate:decimal,apprentice_rate:decimal,start_date:date,end_date:date,billing_cycle_day:integer,service_manager:fk:User,billing_owner:fk:User,excess_policy:text'
~~~

The service plan captures the fixed monthly charge, included minimum hours, and apprentice/associate excess-hour rates. Rate snapshots must also be stored on assignments and invoices so later price changes do not rewrite history.

## Phase 3 — requests, assignments and communication

~~~text
# UNEXECUTED
php boilerplate/generate_files.php servicerequest 'request_number:text,clientorganization:fk:Clientorganization,clientserviceplan:fk:Clientserviceplan,requester:fk:User,title:text,description:text,priority:text,request_status:text,desired_due_date:date,triaged_by:fk:User,triaged_at:datetime,sla_due_at:datetime,closed_at:datetime'
php boilerplate/generate_files.php requeststatushistory 'servicerequest:fk:Servicerequest,from_status:text,to_status:text,reason:text,changed_by:fk:User,changed_at:datetime'
php boilerplate/generate_files.php requestattachment 'servicerequest:fk:Servicerequest,uploaded_by:fk:User,original_name:text,storage_key:text,mime_type:text,file_size:integer,visibility:text'
php boilerplate/generate_files.php workassignment 'servicerequest:fk:Servicerequest,professionalprofile:fk:Professionalprofile,assigned_by:fk:User,assignment_role:text,worker_level:text,rate_currency:text,hourly_rate_snapshot:decimal,assigned_at:datetime,due_at:datetime,assignment_status:text,accepted_at:datetime,completed_at:datetime,supervisor_assignment:fk:Workassignment'
php boilerplate/generate_files.php requestmessage 'servicerequest:fk:Servicerequest,author:fk:User,body:text,visibility:text,message_type:text,parent_message:fk:Requestmessage,edited_at:datetime'
php boilerplate/generate_files.php messageattachment 'requestmessage:fk:Requestmessage,uploaded_by:fk:User,original_name:text,storage_key:text,mime_type:text,file_size:integer'
~~~

Messages require client-visible and internal visibility modes, mention notifications, attachments, unread counts, thread history, and a full audit trail. Apprentice assignments must support a supervisor and must not expose another client’s records.

## Phase 4 — time, billing and manual payment recording

~~~text
# UNEXECUTED
php boilerplate/generate_files.php timelog 'workassignment:fk:Workassignment,work_date:date,hours:decimal,description:text,billable:boolean,approval_status:text,submitted_at:datetime,approved_by:fk:User,approved_at:datetime,rejection_reason:text'
php boilerplate/generate_files.php timeadjustment 'timelog:fk:Timelog,hours_delta:decimal,reason:text,requested_by:fk:User,approved_by:fk:User,approved_at:datetime'
php boilerplate/generate_files.php billingperiod 'clientserviceplan:fk:Clientserviceplan,period_start:date,period_end:date,currency:text,monthly_fee_snapshot:decimal,included_hours_snapshot:decimal,consumed_hours:decimal,excess_hours:decimal,billing_status:text,calculated_at:datetime,calculated_by:fk:User'
php boilerplate/generate_files.php invoice 'invoice_number:text,clientorganization:fk:Clientorganization,billingperiod:fk:Billingperiod,issue_date:date,due_date:date,currency:text,subtotal:decimal,tax_total:decimal,total:decimal,invoice_status:text,approved_by:fk:User,issued_by:fk:User,issued_at:datetime'
php boilerplate/generate_files.php invoiceitem 'invoice:fk:Invoice,item_type:text,description:text,quantity:decimal,unit_rate:decimal,net_amount:decimal,tax_amount:decimal,line_total:decimal,serviceoffering:fk:Serviceoffering,worker_level:text'
php boilerplate/generate_files.php invoiceusage 'invoiceitem:fk:Invoiceitem,timelog:fk:Timelog,timeadjustment:fk:Timeadjustment,included_hours:decimal,excess_hours:decimal,rate_snapshot:decimal,amount:decimal'
php boilerplate/generate_files.php creditnote 'credit_number:text,invoice:fk:Invoice,reason:text,amount:decimal,currency:text,approved_by:fk:User,issued_by:fk:User,issued_at:datetime'
php boilerplate/generate_files.php payment 'clientorganization:fk:Clientorganization,payment_reference:text,payment_method:text,payment_date:date,amount:decimal,currency:text,evidence_key:text,payment_status:text,received_by:fk:User,verified_by:fk:User,verified_at:datetime,notes:text'
php boilerplate/generate_files.php paymentallocation 'payment:fk:Payment,invoice:fk:Invoice,amount:decimal,allocated_by:fk:User,allocated_at:datetime'
~~~

Initial payments are recorded manually by an authorised billing officer. Payment creation, verification, allocation, correction and reversal must be separate auditable actions. Invoice balance is derived from issued totals, allocations, credit notes and reversals; it must not be a freely editable field.

## Phase 5 — notifications and audit

~~~text
# UNEXECUTED
php boilerplate/generate_files.php notification 'user:fk:User,event_key:text,title:text,body:text,related_type:text,related_id:integer,action_url:text,read_at:datetime,acknowledged_at:datetime'
php boilerplate/generate_files.php notificationdelivery 'notification:fk:Notification,channel:text,recipient:text,provider_reference:text,delivery_status:text,attempts:integer,next_attempt_at:datetime,sent_at:datetime,last_error:text'
php boilerplate/generate_files.php notificationpreference 'user:fk:User,event_key:text,in_app:boolean,email:boolean,sms:boolean,digest_mode:text'
php boilerplate/generate_files.php auditevent 'actor:fk:User,action:text,entity_type:text,entity_id:integer,summary:text,ip_address:text,user_agent:text,correlation_id:text,event_at:datetime'
~~~

## Required post-generation specialisation

Before any generated model is loaded:

1. Introduce migrations as the schema source of truth.
2. Make optional review, approval, completion, end-date, provider and error fields nullable.
3. Add unique constraints, foreign-key indexes, status checks, non-negative hour/amount checks and currency rules.
4. Decide whether money uses integer minor units or fixed-scale decimal handling.
5. Seed and lock approved roles: Administrator, Registered User, Client User, Associate, Apprentice, Service Manager, Billing Officer and Vetting Officer.
6. Keep public registration at Registered User; grant special roles only after an approved application.
7. Apply ViewAccess::allow([...]), POST/AJAX enforcement and record-level organisation/assignment scoping.
8. Replace generic CRUD endpoints with transactional application approval, assignment, time approval, invoice issue, payment allocation/reversal and notification workflows.
9. Move attachments and evidence into private storage with authorised streaming, validation, scanning hooks and retention rules.
10. Group navigation into My Work, Clients, Delivery, Billing, Vetting and Configuration; hide join, history and audit internals.
11. Add idempotency protection for billing runs, notification delivery and any future payment gateway callback.
12. Generate and test first against a disposable database, then lint, review diffs and run workflow/billing tests.

## Proposed role access map

| ID | Role | Primary access |
|---:|---|---|
| 1 | Administrator | Full configuration and oversight |
| 2 | Registered User | Own account and application submission |
| 3 | Client User | Own organisation, requests, messages and invoices |
| 4 | Associate | Assigned work, messages and own time |
| 5 | Apprentice | Assigned work under supervision and own time |
| 6 | Service Manager | Triage, assignment, delivery and time approval |
| 7 | Billing Officer | Billing periods, invoices, manual payments and allocations |
| 8 | Vetting Officer | Client and professional applications |

Role IDs are proposals for review; the current imported framework still seeds only Administrator and General User.

## Review checklist

- Confirm whether “associate” or “affiliate” is the canonical worker label.
- Confirm currencies, tax rules, invoice numbering and payment evidence requirements.
- Confirm monthly included hours are pooled by service, by worker level, or across the whole client account.
- Confirm whether unused included hours expire or roll over.
- Confirm who approves time and whether clients may dispute time before invoicing.
- Confirm apprentice supervision and quality-review rules.
- Confirm service-level targets, escalation paths and notification channels.
- Confirm document retention, privacy, consent and jurisdiction requirements.
- Confirm whether a client can have multiple organisations and multiple billing contacts.
- Confirm reporting needs for profitability, utilisation, ageing, workload and service quality.
