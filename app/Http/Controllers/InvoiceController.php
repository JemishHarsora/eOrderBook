<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use PDF;
use Auth;

class InvoiceController extends Controller
{
    //downloads customer invoice
    public function customer_invoice_download($id)
    {
        $order = Order::with('seller')->findOrFail($id);
        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/')
        ])->loadView('backend.invoices.customer_invoice', compact('order'));
        return $pdf->download('order-' . $order->code . '.pdf');
    }

    //downloads seller invoice
    public function seller_invoice_download($id)
    {
        $order = Order::with('seller')->findOrFail($id);
        $order->view_invoice = 1;
        $order->save();
        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/')
        ])->loadView('backend.invoices.seller_invoice', compact('order'));
        return $pdf->download('order-' . $order->code . '.pdf');
    }

    //downloads admin invoice
    public function admin_invoice_download($id)
    {
        $order = Order::findOrFail($id);
        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/')
        ])->loadView('backend.invoices.admin_invoice', compact('order'));
        return $pdf->download('order-' . $order->code . '.pdf');
    }
}
