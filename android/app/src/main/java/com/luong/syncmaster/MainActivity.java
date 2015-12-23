package com.luong.syncmaster;

import android.content.Intent;
import android.os.Bundle;
import android.support.design.widget.FloatingActionButton;
import android.support.design.widget.Snackbar;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;
import android.view.View;
import android.view.Menu;
import android.view.MenuItem;

import com.luong.syncmaster.network.IOService;
import com.luong.syncmaster.sender.SMSSender;
import com.luong.syncmaster.service.SyncMasterService;

public class MainActivity extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        startBackgroundService();
    }

    public void startBackgroundService() {
        startService(new Intent(this, SyncMasterService.class));
    }
}
