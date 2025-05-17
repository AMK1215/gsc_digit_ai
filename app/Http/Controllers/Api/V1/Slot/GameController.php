<?php

namespace App\Http\Controllers\Api\V1\Slot;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\GameProviderResource;
use App\Http\Resources\Api\V1\GameTypeResource;
use App\Http\Resources\GameDetailResource;
use App\Http\Resources\GameListResource;
use App\Http\Resources\HotGameDetailResource;
use App\Http\Resources\Slot\HotGameListResource;
use App\Models\Admin\GameList;
use App\Models\Admin\GameType;
use App\Models\Admin\Product;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    use HttpResponses;

    //game_types
    public function gameType()
    {
        $gameTypes = GameType::where('status', 1)->get();

        return $this->success(GameTypeResource::collection($gameTypes));
    }

    //providers
    public function gameTypeProducts($gameTypeID)
    {
        $gameType = GameType::with(['products' => function ($query) {
            $query->where('status', 1);
            $query->orderBy('order', 'asc');
        }])->where('id', $gameTypeID)->where('status', 1)
            ->first();

        return $this->success(GameProviderResource::collection($gameType->products), 'Game Detail Successfully');
    }

    //game_lists
    public function gameList($product_id, $game_type_id, Request $request)
    {
        $gameLists = GameList::with('product')
            ->where('product_id', $product_id)
            ->where('game_type_id', $game_type_id)
            ->where('status', 1)
            ->where('game_name', 'like', '%'.$request->name.'%')
            ->paginate(9);

        return GameDetailResource::collection($gameLists);
    }

    //hot_games
    public function HotgameList()
    {
        $gameLists = Product::whereHas('gameLists', function ($query) {
            $query->where('hot_status', 1);
        })->with(['gameLists' => function ($query) {
            $query->where('hot_status', 1);
            $query->where('status', 1);
            $query->with('gameType');
        }])
            ->get();

        return $this->success(HotGameDetailResource::collection($gameLists), 'Hot Game Detail Successfully');
    }

    public function allGameProducts()
    {
        $gameTypes = GameType::with(['products' => function ($query) {
            $query->where('status', 1);
            $query->orderBy('order', 'asc');
        }])->where('status', 1)
            ->get();

        return $this->success($gameTypes);
    }

    public function getGameDetail($provider_id, $game_type_id)
    {
        $gameLists = GameList::where('provider_id', $provider_id)
            ->where('game_type_id', $game_type_id)->get();

        return $this->success(GameDetailResource::collection($gameLists), 'Game Detail Successfully');
    }

    public function deleteGameLists(Request $request)
    {
        // Validate the input
        $validated = $request->validate([
            'game_type_id' => 'required|integer',
            'product_id' => 'required|integer',
            //'game_provide_name' => 'required|string|max:100',
        ]);

        $gameTypeId = $validated['game_type_id'];
        $productId = $validated['product_id'];
        //$gameProvideName = $validated['game_provide_name'];

        // Perform the deletion
        $deleted = DB::table('game_lists')
            ->where('game_type_id', $gameTypeId)
            ->where('product_id', $productId)
            //->where('game_provide_name', $gameProvideName)
            ->delete();

        if ($deleted) {
            return response()->json(['message' => 'Game lists deleted successfully.'], 200);
        }

        return response()->json(['message' => 'No records found for the provided criteria.'], 404);
    }
}
