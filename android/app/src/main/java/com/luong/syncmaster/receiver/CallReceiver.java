package com.luong.syncmaster.receiver;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.telephony.TelephonyManager;

import com.luong.syncmaster.listener.SyncEventListener;

public class CallReceiver extends BroadcastReceiver {
    private SyncEventListener syncEventListener;

    public CallReceiver(SyncEventListener syncEventListener) {
        this.syncEventListener = syncEventListener;
    }

    @Override
    public void onReceive(Context mContext, Intent intent) {
        try {
            String state = intent.getStringExtra(TelephonyManager.EXTRA_STATE);
            String phone = intent.getStringExtra(TelephonyManager.EXTRA_INCOMING_NUMBER);
            // Phone Is Ringing
            if (state.equals(TelephonyManager.EXTRA_STATE_RINGING)) {
                syncEventListener.onEvent(SyncEventListener.TYPE_IN_CALL, phone);
            }
            // Call Received
            else if (state.equals(TelephonyManager.EXTRA_STATE_OFFHOOK)) {
            }
            // Phone Is Idle
            else if (state.equals(TelephonyManager.EXTRA_STATE_IDLE)) {
            }
        } catch (Exception e) {

        }

    }
}
