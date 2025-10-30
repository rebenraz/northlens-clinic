<?php 
    session_start();
    
    if (isset($_SESSION['user_id']) && !(isset($_SESSION['role']) && $_SESSION['role'] == 'admin')) {
        return;
    }
    $listsofservices = [];

    include 'db/db.php';
    $stmt = $conn->prepare("SELECT * FROM services");
   $stmt->execute();
   $result = $stmt->get_result();
   $listsofservices = $result->fetch_all(MYSQLI_ASSOC);
   $stmt->close();
   $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Services - Northlens</title>
  <link href="src/output.css" rel="stylesheet">
  <style>
    @media (min-width: 1024px) {
      .lg\:w-3\/4 {
        width: 75%;
      }
      .lg\:w-2\/5 {
        width: 40%;
      }
      .lg\:w-3\/5 {
        width: 60%;
      }
    }
  </style>
</head>
<body class="bg-gray-50 min-h-screen">
  <?php include 'header/header.php'; ?>

  <main class="p-2 lg:w-3/4 mx-auto">
      <div class="mb-8 mt-5">
        <h1 class="text-2xl font-bold text-blue-700">Manage Services</h1>
        <p class="text-gray-600">Add and manage services in your clinic.</p>
      </div>

      <!-- Main Grid -->
      <div class="flex flex-col lg:flex-row gap-6 w-full">
        
        <!-- Service Form -->
        <form class="bg-white  rounded shadow-md md:w-full lg:w-2/5 space-y-4" method="POST" action="crud/addService.php" enctype="multipart/form-data">
          <h2 class="text-xl p-6 font-semibold bg-blue-600 text-white">+ Add New Service</h2>

          <div class="px-6">
            <label class="block font-medium">Service Name</label>
            <input required type="text" name="name" class="w-full border px-4 py-2 rounded" placeholder="Ex: Eye Exam" />
          </div>

          <div class="px-6">
            <label class="block font-medium">Description</label>
            <textarea  class="w-full border px-4 py-2 rounded h-24" name="description" placeholder="Service description..."></textarea>
          </div>

          <div class="grid grid-cols-1 gap-4 px-6">
            <div>
              <label class="block font-medium">Price (₱)</label>
              <input required type="number" class="w-full border px-4 py-2 rounded" name="price" placeholder="500" />
            </div>
          </div>

          <div class="px-6">
            <label class="block font-medium">Image</label>
            <input type="file" class="w-full border px-4 py-2 rounded" accept=".jpg,.jpeg,.png" name="imageUrl" placeholder="500" />
          </div>

          <div class="px-6">
            <label class="block font-medium">Available</label>
            <select name="available" class="w-full border px-4 py-2 rounded">
              <option value="1">Yes</option>
              <option value="0">No</option>
            </select>
          </div>
          <div class="px-6" style="padding-bottom: 10px;">
            <button type="submit" class="bg-blue-600 w-full text-white px-6 py-2 rounded hover:bg-blue-700">Save Service</button>
          </div>
        </form>

        <!-- Service Table -->
        <div class="lg:w-3/5 md:w-full ">
          <h2 class="text-xl font-semibold  bg-gray-100 p-6">Service List</h2>
          <div class="overflow-x-auto bg-white rounded shadow-md">
            <table class="min-w-full border text-sm">
              <thead class="bg-gray-100 text-left">
                <tr>
                  <th class="py-2 px-4 border">Name</th>
                  <th class="py-2 px-4 border">Price</th>
                  <th class="py-2 px-4 border">Image</th>
                  <th class="py-2 px-4 border">Available</th>
                  <th class="py-2 px-4 border">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                foreach ($listsofservices as $service) { ?>
                  <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 border"><?php echo htmlspecialchars($service['name']); ?></td>
                    <td class="px-4 py-2 border text-blue-600">₱ <?php echo number_format(htmlspecialchars($service['price']), 2); ?></td>
                    <td class="px-4 py-2 border">
                      <img width="100" src="<?php echo $service['image_url'] ?>" alt=""></td>
                    <td class="px-4 py-2 border <?php echo $service['available'] ? 'text-green-500' : 'text-red-500'; ?> "><?php echo $service['available'] ? 'Yes' : 'No'; ?></td>
                    <td class="px-4 py-2 border ">
                      <button onclick="openEditModal(<?php echo $service['id']; ?>)" class="text-black hover:underline">
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            fill="currentColor" class="w-5 h-5">
                          <path d="M21.731 2.269a2.625 2.625 0 00-3.712 0l-1.086 1.086 3.712 3.712 1.086-1.086a2.625 2.625 0 000-3.712zM3 17.25V21h3.75l10.606-10.606-3.712-3.712L3 17.25z" />
                        </svg>
                      </button>
                      <button onclick="openDeleteModal(<?php echo $service['id']; ?>)" class="text-black hover:underline"> 
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            fill="currentColor" class="w-5 h-5">
                          <path fill-rule="evenodd"
                                d="M9 3a1 1 0 00-1 1v1H4.75a.75.75 0 000 1.5h.54l.7 12.39A2.25 2.25 0 008.23 21h7.54a2.25 2.25 0 002.24-2.11l.7-12.39h.54a.75.75 0 000-1.5H16V4a1 1 0 00-1-1H9zm2.25 5.25a.75.75 0 011.5 0v8.25a.75.75 0 01-1.5 0V8.25zM8.25 8.25a.75.75 0 011.5 0v8.25a.75.75 0 01-1.5 0V8.25zm6 0a.75.75 0 011.5 0v8.25a.75.75 0 01-1.5 0V8.25z"
                                clip-rule="evenodd" />
                        </svg>
                      </button>
                    </td>
                  </tr>

                  
                    <!-- Edit Modal -->
                    <div id="editModal<?php echo $service['id']; ?>" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                        <div class="bg-white w-full max-w-md p-6 rounded shadow-lg">
                        <h3 class="text-lg font-semibold mb-4">Edit Service</h3>
                            <form method="POST" action="crud/updateService.php" enctype="multipart/form-data">

                                <div class="space-y-3">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($service['id']); ?>" />
                                    <input required type="text" name="name" value="<?php echo htmlspecialchars($service['name']); ?>" class="w-full px-4 py-2 border rounded" placeholder="Service Name" />
                                    <textarea class="w-full px-4 py-2 border rounded h-20" name="description" placeholder="Description"><?php echo htmlspecialchars($service['description']); ?></textarea>
                                    <div class="flex gap-2">
                                      <input required type="number" name="price" class="w-full px-4 py-2 border rounded" value="<?php echo htmlspecialchars($service['price']); ?>" placeholder="Price" />
                                    </div>
                                    <div  >
                                      <label class="block font-medium">Image</label>
                                      <input type="file" class="w-full border px-4 py-2 rounded" accept=".jpg,.jpeg,.png" name="imageUrl" placeholder="500" />
                                    </div>
                                    <select class="w-full px-4 py-2 border rounded" name="available">
                                    <option value="1" <?php echo $service['available'] ? 'selected' : ''; ?>>Available</option>
                                    <option value="0" <?php echo !$service['available'] ? 'selected' : ''; ?>>Not Available</option>
                                    </select>
                                </div>

                                <div class="mt-6 flex justify-end gap-3">
                                    <button  type="button"  onclick="closeEditModal(<?php echo $service['id']; ?>)" class="text-gray-600">Cancel</button>
                                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Delete Modal -->
                    <div id="deleteModal<?php echo $service['id']; ?>" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                        <div class="bg-white w-full max-w-sm p-6 rounded shadow-lg text-center">
                        <h3 class="text-lg font-semibold mb-4 text-red-600">Delete Service?</h3>
                        <p class="mb-4 text-gray-700">Are you sure you want to delete this service? This action cannot be undone.</p>
                        <div class="flex justify-center gap-4">
                            <button type="button" onclick="closeDeleteModal(<?php echo $service['id']; ?>)" class="px-4 py-2 border rounded text-gray-700">Cancel</button>
                            <a href="crud/deleteService.php?id=<?php echo $service['id']; ?>" class="px-4 py-2 bg-red-600 text-white rounded">Delete</a>
                        </div>
                        </div>
                    </div>
                <?php } ?>
                <!-- Add more dynamic rows as needed -->
              </tbody>
            </table>
          </div>
        </div>
      </div>
  </main>
 


  <!-- Scripts -->
  <script>
    const editModal = document.getElementById('editModal');
    const deleteModal = document.getElementById('deleteModal');

    function openEditModal(id) {
        const editModal = document.getElementById('editModal' + id);
      editModal.classList.remove('hidden');
    }

    function closeEditModal(id) {
      const editModal = document.getElementById('editModal' + id);
      editModal.classList.add('hidden');
    }

    function openDeleteModal(id) {
        const deleteModal = document.getElementById('deleteModal' + id);
        deleteModal.classList.remove('hidden');
    }

    function closeDeleteModal(id) {
    const deleteModal = document.getElementById('deleteModal' + id);
      deleteModal.classList.add('hidden');
    }
  </script>
</body>
</html>
