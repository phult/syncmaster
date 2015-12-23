package com.luong.syncmaster.sender;

import android.telephony.SmsManager;

/**
 * Created by phuluong on 12/6/15.
 */
public class SMSSender {
    static SMSSender instance;
    public static SMSSender getInstance(){
        if(instance==null){
            instance=new SMSSender();
        }
        return instance;
    }
    public boolean send(String phoneNumber,String msg) {
        SmsManager smsManager = SmsManager.getDefault();
        smsManager.sendTextMessage(phoneNumber, null, msg, null, null);
        return true;

    }
}
