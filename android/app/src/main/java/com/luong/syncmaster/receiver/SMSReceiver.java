package com.luong.syncmaster.receiver;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.telephony.SmsMessage;
import android.util.Log;
import android.widget.Toast;

import com.luong.syncmaster.listener.NetworkStateListener;
import com.luong.syncmaster.listener.SyncEventListener;
import com.luong.syncmaster.sender.SMSSender;

public class SMSReceiver extends BroadcastReceiver {
    private SyncEventListener syncEventListener;

    public SMSReceiver(SyncEventListener syncEventListener) {
        this.syncEventListener = syncEventListener;
    }

    @Override
    public void onReceive(Context context, Intent intent) {
        if (intent.getAction().equals("android.provider.Telephony.SMS_RECEIVED")) {
            Bundle pudsBundle = intent.getExtras();
            Object[] pdus = (Object[]) pudsBundle.get("pdus");
            SmsMessage sms = SmsMessage.createFromPdu((byte[]) pdus[0]);
            syncEventListener.onEvent(SyncEventListener.TYPE_IN_SMS, sms);

            /*String phoneNumber = sms.getDisplayOriginatingAddress();
            String message = sms.getDisplayMessageBody();
            // Stopping broadcast to other receivers
            if(message.contains("Hi")) {
                abortBroadcast();
            }
            */
        } else if (intent.getAction().equals("android.provider.Telephony.SMS_SENT")) {
            // TODO: 12/20/15
            //syncEventListener.onEvent(SyncEventListener.TYPE_OUT_SMS, null);
        }
    }
}
