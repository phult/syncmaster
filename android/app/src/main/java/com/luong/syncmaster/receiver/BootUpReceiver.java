package com.luong.syncmaster.receiver;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.widget.Toast;

import com.luong.syncmaster.MainActivity;
import com.luong.syncmaster.service.SyncMasterService;

public class BootUpReceiver extends BroadcastReceiver {
    public BootUpReceiver() {
    }

    @Override
    public void onReceive(Context context, Intent intent) {
        Toast.makeText(context, "SyncMaster's ready!", Toast.LENGTH_LONG).show();
        /****** For Start Activity *****/
        /*Intent i = new Intent(context, MainActivity.class);
        i.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
        context.startActivity(i);*/

        /***** For start Service  ****/
        context.startService(new Intent(context, SyncMasterService.class));
    }
}
