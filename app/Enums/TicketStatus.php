<?php

namespace App\Enums;

enum TicketStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case ApprovedByAdmin1 = 'approved_by_admin1';
    case ApprovedByAdmin2 = 'approved_by_admin2';
    case RejectedByAdmin1 = 'rejected_by_admin1';
    case RejectedByAdmin2 = 'rejected_by_admin2';
    case SentToWebService = 'sent_to_webservice';
}