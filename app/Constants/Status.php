<?php

namespace App\Constants;

class Status {

    const ENABLE  = 1;
    const DISABLE = 0;

    const YES = 1;
    const NO  = 0;

    const VERIFIED   = 1;
    const UNVERIFIED = 0;

    const PAYMENT_INITIATE = 0;
    const PAYMENT_SUCCESS  = 1;
    const PAYMENT_PENDING  = 2;
    const PAYMENT_REJECT   = 3;

    CONST TICKET_OPEN   = 0;
    CONST TICKET_ANSWER = 1;
    CONST TICKET_REPLY  = 2;
    CONST TICKET_CLOSE  = 3;

    CONST PRIORITY_LOW    = 1;
    CONST PRIORITY_MEDIUM = 2;
    CONST PRIORITY_HIGH   = 3;

    const USER_ACTIVE = 1;
    const USER_BAN    = 0;

    const KYC_UNVERIFIED = 0;
    const KYC_PENDING    = 2;
    const KYC_VERIFIED   = 1;

    const SINGLE_ITEM  = 1;
    const EPISODE_ITEM = 2;

    const FREE_VERSION = 0;
    const PAID_VERSION = 1;
    const RENT_VERSION = 2;

    const TRAILER = 1;

    const CURRENT_SERVER       = 0;
    const FTP_SERVER           = 1;
    const WASABI_SERVER        = 2;
    const DIGITAL_OCEAN_SERVER = 3; 
    const AWS_CDN = 4;
    const LINK = 5;


    

    const WATCH_PARTY_REQUEST_PENDING  = 0;
    const WATCH_PARTY_REQUEST_ACCEPTED = 1;
    const WATCH_PARTY_REQUEST_REJECTED = 2;

}