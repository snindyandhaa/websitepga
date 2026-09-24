namespace App\Http\Controllers;

use App\Models\AssetCar;
use Illuminate\Http\Request;

class AssetCarController extends Controller
{
    public function index()
    {
        return response()->json(AssetCar::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'license_no' => 'required|unique:asset_cars',
        ]);

        $asset = AssetCar::create($request->all());
        return response()->json($asset, 201);
    }
}