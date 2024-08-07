<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderProduct;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="OrderProducts",
 *     description="API Endpoints for managing order products"
 * )
 */
/**
 * @OA\Schema(
 *     schema="OrderProduct",
 *     type="object",
 *     @OA\Property(property="id", type="integer", format="int64"),
 *     @OA\Property(property="order_id", type="integer", format="int64"),
 *     @OA\Property(property="product_id", type="integer", format="int64"),
 *     @OA\Property(property="quantity", type="integer", format="int32"),
 * )
 */
class OrderProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/order-products",
     *     operationId="getOrderProductsList",
     *     tags={"OrderProducts"},
     *     summary="Get list of order products",
     *     description="Returns a list of order products",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/OrderProduct")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return OrderProduct::all();
    }

    /**
     * @OA\Post(
     *     path="/api/v1/order-products",
     *     operationId="storeOrderProduct",
     *     tags={"OrderProducts"},
     *     summary="Create a new order product",
     *     description="Creates a new order product",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/OrderProduct")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/OrderProduct")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $orderProduct = OrderProduct::create($request->all());
        return response()->json($orderProduct, 201);
    }
    /**
     * @OA\Get(
     *     path="/api/v1/order-products/{id}",
     *     operationId="getOrderProductById",
     *     tags={"OrderProducts"},
     *     summary="Get order product by ID",
     *     description="Returns a single order product",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/OrderProduct")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order Product not found"
     *     )
     * )
     */
    public function show($id)
    {
        $orderProduct = OrderProduct::findOrFail($id);
        return response()->json($orderProduct);
    }
    /**
     * @OA\Put(
     *     path="/api/v1/order-products/{id}",
     *     operationId="updateOrderProduct",
     *     tags={"OrderProducts"},
     *     summary="Update an existing order product",
     *     description="Updates an existing order product",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/OrderProduct")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/OrderProduct")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order Product not found"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $orderProduct = OrderProduct::findOrFail($id);
        $orderProduct->update($request->all());
        return response()->json($orderProduct);
    }
    /**
     * @OA\Delete(
     *     path="/api/v1/order-products/{id}",
     *     operationId="deleteOrderProduct",
     *     tags={"OrderProducts"},
     *     summary="Delete an order product",
     *     description="Deletes an order product",
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
     *         description="Order Product not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $orderProduct = OrderProduct::findOrFail($id);
        $orderProduct->delete();
        return response()->json(null, 204);
    }
}
