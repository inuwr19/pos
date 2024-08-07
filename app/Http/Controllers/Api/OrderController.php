<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Orders",
 *     description="API Endpoints for managing orders"
 * )
 */
/**
 * @OA\Schema(
 *     schema="Order",
 *     type="object",
 *     @OA\Property(property="id", type="integer", format="int64"),
 *     @OA\Property(property="customer_id", type="integer", format="int64"),
 *     @OA\Property(property="total_amount", type="number", format="float"),
 *     @OA\Property(property="status", type="string"),
 * )
 */
class OrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/orders",
     *     operationId="getOrdersList",
     *     tags={"Orders"},
     *     summary="Get list of orders",
     *     description="Returns list of orders",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Order"))
     *     )
     * )
     */
    public function index()
    {
        return Order::all();
    }
    /**
     * @OA\Post(
     *     path="/api/v1/orders",
     *     operationId="storeOrder",
     *     tags={"Orders"},
     *     summary="Create a new order",
     *     description="Creates a new order",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $order = Order::create($request->all());
        return response()->json($order, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/orders/{id}",
     *     operationId="getOrderById",
     *     tags={"Orders"},
     *     summary="Get order by ID",
     *     description="Returns a single order",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     )
     * )
     */
    public function show($id)
    {
        $order = Order::findOrFail($id);
        return response()->json($order);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/orders/{id}",
     *     operationId="updateOrder",
     *     tags={"Orders"},
     *     summary="Update an existing order",
     *     description="Updates an existing order",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update($request->all());
        return response()->json($order);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/orders/{id}",
     *     operationId="deleteOrder",
     *     tags={"Orders"},
     *     summary="Delete an order",
     *     description="Deletes an order",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="successful operation"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return response()->json(null, 204);
    }
}
