<?php

namespace App\Http\Controllers;

/**
 * @OA\Schema(
 *     schema="Transaction",
 *     type="object",
 *     title="Transaction",
 *     description="Data of a successful transaction",
 *     required={"id", "payer", "payee", "value", "created_at", "updated_at"},
 *     @OA\Property(property="id", type="integer", example=123),
 *     @OA\Property(property="payer", type="integer", example=1, description="Payer ID"),
 *     @OA\Property(property="payee", type="integer", example=2, description="Payee ID"),
 *     @OA\Property(property="value", type="number", format="float", example=150.50, description="Transaction value"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-29T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-06-29T12:30:00Z")
 * )
 *
 *
 *
 * @OA\Server(url="http://localhost/api"),
 * @OA\Info(title="Simplified Banking API", version="0.0.1")
 * @OA\Get(
 *     path="/",
 *     summary="Connection status",
 *     tags={"Transactions"},
 *     @OA\Response(
 *         response="200",
 *         description="OK"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/transfer",
 *     operationId="transferMoney",
 *     tags={"Transactions"},
 *     summary="Transactions between wallets",
 *     description="Execute a transfer",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"payer_wallet_id", "payee_wallet_id", "value"},
 *             @OA\Property(property="payer_wallet_id", type="integer", example=1, description="Payer ID"),
 *             @OA\Property(property="payee_wallet_id", type="integer", example=2, description="Payee ID"),
 *             @OA\Property(property="value", type="number", format="float", example=100.00, description="Amount to be transferred")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Transfer successful",
 *         @OA\JsonContent(ref="#/components/schemas/Transaction")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Insufficient balance.",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Insufficient balance.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Unauthorized transfer or merchant trying to transfer",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Retailers cannot send money.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal error processing transaction",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Transaction failed")
 *         )
 *     )
 * )
 */

abstract class Controller
{
    //
}
