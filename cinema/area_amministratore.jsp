<%@ page import="java.sql.Connection, java.sql.DriverManager, java.sql.PreparedStatement, java.sql.ResultSet, java.sql.SQLException" %>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Area Amministratore - Cinema Hub</title>
    <link rel="stylesheet" href="index.css?v=<%= new java.util.Date().getTime() %>">
    <style>
        .search-box {
            margin-bottom: 20px;
        }

        .search-box input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .scrollable-table {
            max-height: 500px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin: 20px 0;
        }

        .scrollable-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .scrollable-table th,
        .scrollable-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .scrollable-table th {
            background-color: #333;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .section-header {
            background-color: #333;
            color: white;
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .filter-box {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            align-items: center;
        }
        
        .filter-box label {
            font-weight: bold;
            margin-right: 5px;
        }
        
        .filter-box input, .filter-box button {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .filter-box button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        
        .filter-box button:hover {
            background-color: #45a049;
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #ffffff;
            margin: 5% auto;
            padding: 25px;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .modal-content h2 {
            margin: 0 0 20px 0;
            color: #333;
            font-size: 1.5em;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: rgb(255, 255, 255);
            font-weight: bold;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #4CAF50;
            outline: none;
            box-shadow: 0 0 5px rgba(76,175,80,0.2);
        }

        .form-actions {
            margin-top: 25px;
            text-align: right;
        }

        .form-actions button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-left: 10px;
        }

        .save-btn {
            background-color: #4CAF50;
            color: white;
        }

        .save-btn:hover {
            background-color: #45a049;
        }

        .cancel-btn {
            background-color: #f44336;
            color: white;
        }

        .cancel-btn:hover {
            background-color: #da190b;
        }

        .close {
            color: #666;
            float: right;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            margin: -15px -15px 0 0;
        }

        .close:hover {
            color: #333;
        }
        
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
        }
        
        .search-form {
            display: flex;
            align-items: center;
        }
        
        .search-container {
            display: flex;
            align-items: center;
        }
        
        .search-container input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
        }
        
        .search-container button {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            background-color: #4CAF50;
            color: white;
            margin-left: 10px;
        }
        
        .search-container button:hover {
            background-color: #45a049;
        }
        
        .proiezioni-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .proiezione-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            display: flex;
            flex-direction: column;
        }

        .proiezione-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .proiezione-image {
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.1);
            flex: 1;
        }

        .proiezione-image img {
            position: static;
            width: 100%;
            height: auto;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .proiezione-info {
            padding: 1rem;
            background: rgba(0, 0, 0, 0.2);
            flex: 0 0 auto;
        }

        .proiezione-info h3 {
            color: #ff4444;
            margin: 0 0 1rem 0;
            font-size: 1.25rem;
            text-align: center;
        }

        .proiezione-info p {
            color: #ffffff;
            margin: 0.5rem 0;
            font-size: 0.95rem;
            text-align: center;
        }

        .proiezione-info .prenota-btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background: #ff4444;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            transition: transform 0.3s ease, background 0.3s ease;
            margin-top: 1rem;
        }

        .proiezione-info .prenota-btn:hover {
            transform: translateY(-2px);
            background: #ff2d2d;
        }

        .proiezione-info .prenota-btn:active {
            transform: translateY(0);
        }

        .no-proiezioni {
            text-align: center;
            padding: 2rem;
            color: #ff4444;
            font-size: 1.25rem;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">CINEMA HUB</div>
            <ul>
                <li><a href="index.jsp">Home</a></li>
                <li><a href="film.jsp">Film</a></li>
                <li><a href="#about">Chi siamo</a></li>
                <li><a href="#contact">Contatti</a></li>
                <li><span>Benvenuto, <%= session.getAttribute("username") %></span></li>
                <li><a href="logout.jsp" class="logout-btn">Esci</a></li>
            </ul>
        </nav>
    </header>

    <main class="admin-panel">
        <%
            // Display error messages (consolidated)
            String errorMsg = (String) session.getAttribute("error");
            if (errorMsg == null) {
                errorMsg = request.getParameter("error");
            }
            if (errorMsg != null) {
                out.println("<div class='error-message'>" + errorMsg + "</div>");
                session.removeAttribute("error");
            }
        %>
        <h1>Benvenuto, <%= session.getAttribute("username") %></h1>

        <nav class="admin-nav">
            <a href="#gestione-film" class="active">Gestione Film</a>
            <a href="#gestione-proiezioni">Gestione Proiezioni</a>
            <a href="#gestione-utenti">Gestione Utenti</a>
        </nav>

        <%
            Connection conn = null;
            PreparedStatement pstmt = null;
            ResultSet rs = null;
            
            try {
                Class.forName("com.mysql.cj.jdbc.Driver");
                conn = DriverManager.getConnection("jdbc:mysql://localhost:3306/cinema", "root", "");
                
                pageContext.setAttribute("dbConnection", conn);
            } catch(Exception e) {
                out.println("<div class=\"error-message\">Errore di connessione al database: " + e.getMessage() + "</div>");
            }
        %>

        <!-- Gestione Film Section -->
        <section id="gestione-film">
            <h2 class="section-header">Gestione Film</h2>
            
            <!-- Form per inserimento nuovo film -->
            <div class="admin-form">
                <h3>Inserisci un nuovo film</h3>
                <form action="manageMovies.jsp" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Titolo</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="director">Regista</label>
                        <input type="text" id="director" name="director" required>
                    </div>
                    <div class="form-group">
                        <label for="year">Anno</label>
                        <input type="number" id="year" name="year" min="1900" max="<%= new java.util.Date().getYear() + 1900 %>" required>
                    </div>
                    <div class="form-group">
                        <label for="image">Immagine</label>
                        <input type="file" id="image" name="image" accept="image/*" required>
                    </div>
                    <button type="submit" class="admin-btn">Aggiungi Film</button>
                </form>
            </div>

            <!-- Lista dei film esistenti -->
            <div class="admin-table">
                <h3 class="section-header">Film Attuali</h3>
                <div class="search-box">
                    <input type="text" id="filmSearch" placeholder="Cerca per codice film...">
                </div>
                <div class="scrollable-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Codice Film</th>
                                <th>Titolo</th>
                                <th>Regista</th>
                                <th>Anno</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <%
                                try {
                                    String sql = "SELECT * FROM film ORDER BY Anno DESC";
                                    pstmt = conn.prepareStatement(sql);
                                    rs = pstmt.executeQuery();
                                    
                                    while(rs.next()) {
                                        String codiceFilm = rs.getString("Codice_film");
                                        String titolo = rs.getString("Titolo");
                                        String regista = rs.getString("Regista");
                                        String anno = rs.getString("Anno");
                                        
                                        // Pulisci il codice per la consistenza
                                        String cleanCode = codiceFilm.trim().toUpperCase();
                            %>
                                <tr>
                                    <td><%= codiceFilm %></td>
                                    <td><%= titolo %></td>
                                    <td><%= regista %></td>
                                    <td><%= anno %></td>
                                    <td>
                                        <a href="editMovie.jsp?code=<%= cleanCode %>&fromAdmin=true" class="admin-btn">Modifica</a>
                                        <a href="deleteMovie.jsp?code=<%= cleanCode %>" class="admin-btn" onclick="return confirm('Sei sicuro di voler eliminare questo film?')">Elimina</a>
                                    </td>
                                </tr>
                            <%
                                    }
                                } catch(Exception e) {
                                    out.println("<tr><td colspan=\"5\" class=\"error-message\">Errore nel caricamento dei film: " + e.getMessage() + "</div>");
                                } finally {
                                    if (rs != null) rs.close();
                                    if (pstmt != null) pstmt.close();
                                }
                            %>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Gestione Proiezioni Section -->
        <section id="gestione-proiezioni">
            <h2 class="section-header">Gestione Proiezioni</h2>
            
            <!-- Form per inserimento nuova proiezione -->
            <div class="admin-form">
                <h3>Inserisci una proiezione</h3>
                <form action="manageProjections.jsp" method="post">
                    <div class="form-group">
                        <label for="filmCode">Codice Film</label>
                        <select id="filmCode" name="filmCode" required>
                            <%
                                try {
                                    String sqlFilms = "SELECT Codice_film, Titolo FROM film ORDER BY Titolo";
                                    pstmt = conn.prepareStatement(sqlFilms);
                                    rs = pstmt.executeQuery();
                                    
                                    while(rs.next()) {
                                        String codice = rs.getString("Codice_film");
                                        String titolo = rs.getString("Titolo");
                            %>
                                <option value="<%= codice %>">
                                    <%= codice %> - <%= titolo %>
                                </option>
                            <%
                                    }
                                } catch(Exception e) {
                                    out.println("<option value=\"\">Errore nel caricamento dei film: " + e.getMessage() + "</option>");
                                } finally {
                                    if (rs != null) rs.close();
                                    if (pstmt != null) pstmt.close();
                                }
                            %>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="date">Data</label>
                        <input type="date" id="date" name="date" required>
                    </div>
                    <div class="form-group">
                        <label for="time">Ora</label>
                        <input type="time" id="time" name="time" required>
                    </div>
                    <button type="submit" class="admin-btn">Aggiungi Proiezione</button>
                </form>
            </div>

            <!-- Lista delle proiezioni -->
            <div class="admin-table">
                <h3 class="section-header">Proiezioni Programmate</h3>
                <div class="filter-box">
                    <form method="get" action="area_amministratore.jsp" class="filter-form">
                        <div class="form-group">
                            <label for="dateFilter">Filtra per data:</label>
                            <input type="date" id="dateFilter" name="dateFilter" value="<%= new java.text.SimpleDateFormat("yyyy-MM-dd").format(new java.util.Date()) %>">
                        </div>
                        <button type="submit" class="filter-btn">Applica Filtro</button>
                        <button type="reset" class="reset-btn">Reimposta</button>
                    </form>
                </div>
                <div class="scrollable-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Codice Proiezione</th>
                                <th>Titolo</th>
                                <th>Data</th>
                                <th>Ora</th>
                                <th>Sala</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <%
                                String selectedDate = request.getParameter("dateFilter");
                                if (selectedDate == null || selectedDate.isEmpty()) {
                                    selectedDate = new java.text.SimpleDateFormat("yyyy-MM-dd").format(new java.util.Date());
                                }
                                
                                try {
                                    Connection dbConn = (Connection) pageContext.getAttribute("dbConnection");
                                    if (dbConn == null) {
                                        throw new SQLException("Connessione al database non trovata");
                                    }
                                    
                                    String sqlProiezioni = "SELECT p.Codice_proiezioni, f.Titolo, p.Data, p.Ora, p.Sala " +
                                                          "FROM proiezioni p " +
                                                          "JOIN film f ON p.Codice_film = f.Codice_film " +
                                                          "WHERE 1=1 " +
                                                          (selectedDate != null && !selectedDate.isEmpty() ? " AND DATE(p.Data) = ?" : "") +
                                                          " ORDER BY p.Data, p.Ora";
                                    
                                    pstmt = dbConn.prepareStatement(sqlProiezioni);
                                    if (selectedDate != null && !selectedDate.isEmpty()) {
                                        pstmt.setDate(1, java.sql.Date.valueOf(selectedDate));
                                    }
                                    
                                    rs = pstmt.executeQuery();
                                    
                                    while(rs.next()) {
                                        String codiceProiezione = rs.getString("Codice_proiezioni");
                                        String titolo = rs.getString("Titolo");
                                        String dataProiezione = rs.getString("Data");
                                        String ora = rs.getString("Ora");
                                        String sala = rs.getString("Sala");
                            %>
                            <tr>
                                <td><%= codiceProiezione %></td>
                                <td><%= titolo %></td>
                                <td><%= dataProiezione %></td>
                                <td><%= ora %></td>
                                <td><%= sala %></td>
                                <td>
                                    <a href="editProjection.jsp?code=<%= codiceProiezione %>" class="admin-btn">Modifica</a>
                                    <a href="deleteProjection.jsp?code=<%= codiceProiezione %>" class="admin-btn" onclick="return confirm('Sei sicuro di voler eliminare questa proiezione?')">Elimina</a>
                                </td>
                            </tr>
                            <%
                                    }
                                } catch (SQLException e) {
                                    out.println("<tr><td colspan='6' class='error-message'>Errore nel caricamento delle proiezioni: " + e.getMessage() + "</td></tr>");
                                } finally {
                                    if (rs != null) rs.close();
                                    if (pstmt != null) pstmt.close();
                                }
                            %>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Gestione Utenti Section -->
        <section id="gestione-utenti">
            <h2 class="section-header">Gestione Utenti</h2>
            
            <%
                String message = request.getParameter("success");
                if (message != null) {
            %>
            <div class="success-message"><%= message %></div>
            <%
                }
                
                String error = request.getParameter("error");
                if (error != null) {
            %>
            <div class="error-message"><%= error %></div>
            <%
                }
            %>
        

            <!-- Tabella utenti -->
            <div class="search-box">
                <input type="text" id="userSearch" placeholder="Cerca utente...">
            </div>
            <div class="scrollable-table">
                <table id="usersTable">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Nome</th>
                            <th>Cognome</th>
                            <th>Telefono</th>
                            <th>Codice Fiscale</th>
                            <th>Ruolo</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <%
                            Connection connUtenti = null;
                            PreparedStatement pstmtUtenti = null;
                            ResultSet rsUtenti = null;
                            
                            try {
                                Class.forName("com.mysql.cj.jdbc.Driver");
                                connUtenti = DriverManager.getConnection("jdbc:mysql://localhost:3306/cinema", "root", "");
                                
                                String sql = "SELECT * FROM utenti ORDER BY username";
                                pstmtUtenti = connUtenti.prepareStatement(sql);
                                rsUtenti = pstmtUtenti.executeQuery();
                                
                                while (rsUtenti.next()) {
                                    String userUsername = rsUtenti.getString("username");
                                    String userNome = rsUtenti.getString("nome");
                                    String userCognome = rsUtenti.getString("cognome");
                                    String userTelefono = rsUtenti.getString("telefono");
                                    String userCodiceFiscale = rsUtenti.getString("codice_fiscale");
                                    String userRuolo = rsUtenti.getString("ruolo");
                        %>
                        <tr>
                            <td><%= userUsername %></td>
                            <td><%= userNome %></td>
                            <td><%= userCognome %></td>
                            <td><%= userTelefono %></td>
                            <td><%= userCodiceFiscale %></td>
                            <td><%= userRuolo %></td>
                            <td>
                                <a href="/cinema/update_user.jsp?username=<%= userUsername %>" class="admin-btn">Modifica</a>
                                <a href="delete_user.jsp?username=<%= userUsername %>" class="admin-btn" onclick="return confirm('Sei sicuro di voler eliminare questo utente?')">Elimina</a>
                            </td>
                        </tr>
                        <%
                                }
                            } catch (SQLException e) {
                                out.println("<tr><td colspan='7' class='error-message'>Errore nel caricamento degli utenti: " + e.getMessage() + "</td></tr>");
                            } finally {
                                try {
                                    if (rsUtenti != null) rsUtenti.close();
                                    if (pstmtUtenti != null) pstmtUtenti.close();
                                    if (connUtenti != null) connUtenti.close();
                                } catch (SQLException e) {
                                    // Ignore close exception
                                }
                            }
                        %>
                    </tbody>
                </table>
            </div>
        </section>

        <footer>
            <p>&copy; 2024 Cinema Hub. All rights reserved.</p>
        </footer>

        <script>
            // Funzione di ricerca film
            document.getElementById('filmSearch').addEventListener('keyup', function() {
                var input = this.value.toLowerCase();
                var table = document.querySelector('#filmTable');
                var rows = table.getElementsByTagName('tr');

                for (var i = 1; i < rows.length; i++) { // Inizia da 1 per saltare l'header
                    var codiceCell = rows[i].getElementsByTagName('td')[0]; // La prima colonna contiene il codice film
                    if (codiceCell) {
                        var codice = codiceCell.textContent || codiceCell.innerText;
                        if (codice.toLowerCase().indexOf(input) > -1) {
                            rows[i].style.display = '';
                        } else {
                            rows[i].style.display = 'none';
                        }
                    }
                }
            });

            // Funzione di ricerca utenti
            document.getElementById('userSearch').addEventListener('keyup', function() {
                var input = this.value.toLowerCase();
                var table = document.querySelector('#usersTable');
                var rows = table.getElementsByTagName('tr');

                for (var i = 1; i < rows.length; i++) { // Inizia da 1 per saltare l'header
                    var found = false;
                    var cells = rows[i].getElementsByTagName('td');
                    
                    // Cerca in tutte le colonne della tabella utenti
                    for (var j = 0; j < cells.length - 1; j++) { // -1 per escludere la colonna delle azioni
                        var cellText = cells[j].textContent || cells[j].innerText;
                        if (cellText.toLowerCase().indexOf(input) > -1) {
                            found = true;
                            break;
                        }
                    }
                    
                    rows[i].style.display = found ? '' : 'none';
                }
            });
        </script>

        <%
            // Close the database connection at the end of the page
            try {
                if (conn != null) conn.close();
            } catch (Exception e) {
                // Handle any errors during cleanup
            }
        %>
    </body>
</html>
