<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function generar(Request $request)
    {
        // Recibimos el tipo de reporte desde el formulario (por defecto 'productos')
        $tipo = $request->input('tipo', 'productos');
        $data = [];
        $titulo = '';

        if ($tipo == 'productos') {
            $titulo = 'Reporte Consolidado de Ventas por Producto';
            $data = DB::table('order_details')
                ->join('products', 'order_details.product_id', '=', 'products.id')
                ->select(
                    'products.name as descripcion',
                    'products.category as categoria',
                    DB::raw('SUM(order_details.quantity) as cantidad'),
                    DB::raw('SUM(order_details.quantity * order_details.unit_price) as total')
                )
                ->groupBy('products.id', 'products.name', 'products.category')
                ->orderByDesc('cantidad')
                ->get();

        } elseif ($tipo == 'proveedores') {
            $titulo = 'Reporte de Gastos por Proveedor';
            // Unimos el detalle con la compra y luego con el proveedor
            $data = DB::table('purchase_details')
                ->join('purchases', 'purchase_details.purchase_id', '=', 'purchases.id')
                ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
                ->select(
                    'suppliers.name as descripcion',
                    DB::raw('"Compras de Insumos" as categoria'), // Campo simulado para mantener la estructura de la tabla
                    DB::raw('SUM(purchase_details.quantity) as cantidad'),
                    DB::raw('SUM(purchase_details.quantity * purchase_details.unit_cost) as total')
                )
                ->groupBy('suppliers.id', 'suppliers.name')
                ->orderByDesc('total')
                ->get();
        }

        $totalGeneral = collect($data)->sum('total');

        // Cargamos la nueva vista formal
        $pdf = Pdf::loadView('reports.formal-report', [
            'tipo' => $tipo,
            'titulo' => $titulo,
            'data' => $data,
            'totalGeneral' => $totalGeneral,
            'fecha' => now()->format('d/m/Y H:i A')
        ]);

        // Formato A4 estándar
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('reporte_'.$tipo.'.pdf');
    }

    public function ticketVenta($id)
    {
        $order = DB::table('orders')->where('id', $id)->first();

        $client = null;
        if ($order->client_id) {
            $client = DB::table('clients')->where('id', $order->client_id)->first();
        }

        $details = DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->where('order_details.order_id', $id)
            ->select('products.name', 'order_details.quantity', 'order_details.unit_price')
            ->get();

        $pdf = Pdf::loadView('reports.ticket-venta', [
            'order' => $order,
            'client' => $client,
            'details' => $details,
        ]);

        // Formato Ticket Térmico 80mm
        $pdf->setPaper([0, 0, 226.77, 600], 'portrait');

        return $pdf->stream('ticket_venta_'.$id.'.pdf');
    }
}
