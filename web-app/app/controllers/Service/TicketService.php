<?php

namespace Service;

use DateTime;
use DB;
use Exception;
use Input;
use NTicket;
use Response;
use Session;
use Ticket;

/**
 * Copyright (C) 2014, MEGAADS - All Rights Reserved
 *
 * This software is released under the terms of the proprietary license.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 * Proprietary and confidential.
 */

/**
 * @author PHI DINH SON
 * Mar 31, 2015 3:27:30 PM
 */
class TicketService extends ServiceController {

    //--------------------------------------------------------------------------
    //  Members
    //--------------------------------------------------------------------------
    //  Initialization
    //--------------------------------------------------------------------------
    //  Getter N Setter
    //--------------------------------------------------------------------------
    //  Method binding
    public function create() {
        $result = array();
        $ticketData = $this->buildTicketData();
        DB::beginTransaction();
        try {
            $ticket = Ticket::create($ticketData);
            //update code:
            $ticket->code = "T" . str_pad($ticket->id, 5, "0", STR_PAD_LEFT);
            $ticket->save();
            //create nTickets
            $nTickets = Input::get("nTickets");
            foreach ( $nTickets as $nTicket ) {
                $nTicket["ticket_id"] = $ticket->id;
                NTicket::create($nTicket);
            }
            DB::commit();
            $result["status"] = TicketService::STATUS_SUCCESSFUL;
            $result["result"] = $ticket->id;
        } catch ( Exception $ex ) {
            DB::rollBack();
            $result["status"] = TicketService::STATUS_FAIL;
            $result["message"] = $ex->getMessage();
        }
        //return
        return Response::json($result);
    }

    public function update() {
        $result = array();
        $ticketData = $this->buildTicketData();
        DB::beginTransaction();
        try {
            $ticket = Ticket::find(Input::get("id"));
            $ticket->update($ticketData);
            $nTickets = Input::get("nTickets");
            $validNTicketIds = array();
            foreach ( $nTickets as $nTicket ) {
                if ( array_key_exists("id", $nTicket) ) {
                    if ( array_key_exists("editting", $nTicket) ) {
                        $oldNTicket = NTicket::find($nTicket["id"]);
                        $oldNTicket->update($nTicket);
                    }
                    $validNTicketIds[] = $nTicket["id"];
                } else {
                    $nTicket["ticket_id"] = $ticket->id;
                    $nTicket["create_time"] = new DateTime();
                    $createdNTicket = NTicket::create($nTicket);
                    $validNTicketIds[] = $createdNTicket->id;
                }
            }
            if ( count($validNTicketIds) > 0 ) {
                NTicket::whereNotIn("id", $validNTicketIds)
                        ->where("ticket_id", "=", $ticket->id)
                        ->delete();
            }
            DB::commit();
            $result["status"] = TicketService::STATUS_SUCCESSFUL;
            $result["result"] = $ticket->id;
        } catch ( Exception $ex ) {
            $result["status"] = TicketService::STATUS_FAIL;
            $result["message"] = $ex->getMessage();
        }
        //return
        return Response::json($result);
    }

    /**
     * Input: id
     * @return type
     */
    public function delete() {
        $result = array();
        $id = Input::get("id");
        DB::beginTransaction();
        try {
            NTicket::where("ticket_id", "=", $id)
                    ->delete();
            Ticket::where("id", "=", $id)
                    ->delete();
            DB::commit();
            $result["status"] = TicketService::STATUS_SUCCESSFUL;
            $result["result"] = $id;
        } catch ( Exception $ex ) {
            DB::rollBack();
            $result["status"] = TicketService::STATUS_FAIL;
            $result["message"] = $ex->getMessage();
        }
        //return
        return Response::json($result);
    }

    /**
     * Find ticket by:
     * 1. id, code OR:
     * 2. status
     * 3. assigneeId
     * 4. createTimeFrom
     * 5. createTimeTo
     * 6. expectedEndTimeFrom
     * 7. expectedEndTimeTo
     * 8. pageId, pageSize
     * 9. metric: "count"
     * 
     * @return type
     */
    public function find() {
        if ( Input::has("id") ) {
            $result["status"] = TicketService::STATUS_SUCCESSFUL;
            $result["result"] = $this->findByCodeOrId(Input::get("id"), false);
            return Response::json($result);
        } else if ( Input::has("code") ) {
            $result["status"] = TicketService::STATUS_SUCCESSFUL;
            $result["result"] = $this->findByCodeOrId(Input::get("code"), true);
            return Response::json($result);
        }
        //ELSE:
        $query = DB::table("chi_ticket");
        //status
        if ( Input::has("status") ) {
            $query->where("status", "=", Input::get("status"));
        }
        //assigneeId
        if ( Input::has("assigneeId") ) {
            $query->where("assignee_id", "=", Input::get("assigneeId"));
        }
        //createTimeFrom        
        if ( Input::has("createTimeFrom") ) {
            $createTimeFrom = new DateTime();
            $createTimeFrom->setTimestamp(Input::get("createTimeFrom"));
            $query->where("chi_ticket.create_time", ">=", $createTimeFrom);
        }
        //create time to
        if ( Input::has("createTimeTo") ) {
            $createTimeTo = new DateTime();
            $createTimeTo->setTimestamp(Input::get("createTimeTo"));
            $createTimeTo->add(new DateInterval('P1D'));
            $query->where("chi_ticket.create_time", "<", $createTimeTo);
        }
        //expectedEndTimeFrom
        if ( Input::has("expectedEndTimeFrom") ) {
            $expectedEndTimeFrom = new DateTime();
            $expectedEndTimeFrom->setTimestamp(Input::get("expectedEndTimeFrom"));
            $query->where("chi_ticket.expected_end_time", ">=", $expectedEndTimeFrom);
        }
        //expectedEndTimeTo
        if ( Input::has("expectedEndTimeTo") ) {
            $expectedEndTimeTo = new DateTime();
            $expectedEndTimeTo->setTimestamp(Input::get("expectedEndTimeTo"));
            $expectedEndTimeTo->add(new DateInterval('P1D'));
            $query->where("chi_ticket.expected_end_time", "<", $expectedEndTimeTo);
        }
        $query->orderBy("chi_ticket.id", "desc");
        $result = $this->executeQuery($query);
        //return
        return Response::json($result);
    }

    //--------------------------------------------------------------------------
    //  Util
    private function findByCodeOrId( $data, $findByCode = true ) {
        $query = Ticket::query();
        if ( $findByCode ) {
            $query->where("code", "=", $data);
        } else {
            $query->where("id", "=", $data);
        }
        $tickets = $query->get();
        if ( count($tickets) == 0 ) {
            return null;
        }
        //ELSE:
        $retVal = $tickets[0];
        //nTickets
        $nTickets = NTicket::where("ticket_id", "=", $retVal->id)
                ->get();
        $retVal->nTickets = $nTickets;
        return $retVal;
    }

    private function buildTicketData() {
        $retVal = Input::only("title", "content", "status", "assignee_id", "assignee_name");
        if ( Input::has("id") ) {
            $retVal["id"] = Input::get("id");
        } else {
            $retVal["creator_id"] = Session::get("user")->id;
            $retVal["creator_name"] = Session::get("user")->full_name;
            $retVal["create_time"] = new DateTime();
        }
        $retVal["update_time"] = new DateTime();
        $expectedEndTime = new DateTime();
        $expectedEndTime->setTimestamp(Input::get("expected_end_time"));
        $retVal["expected_end_time"] = $expectedEndTime;
        //return
        return $retVal;
    }

    //--------------------------------------------------------------------------
    //  Inner class
}
